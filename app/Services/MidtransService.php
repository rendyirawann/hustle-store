<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Buat Snap token untuk sebuah order.
     */
    public function createSnapToken(Order $order): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->amount,
            ],
            'item_details' => [[
                'id'       => $order->product_code ?: ('ITEM-' . $order->id),
                'price'    => (int) $order->amount,
                'quantity' => 1,
                'name'     => \Illuminate\Support\Str::limit(($order->brand_name ? $order->brand_name . ' - ' : '') . $order->product_name, 50, ''),
            ]],
            'customer_details' => [
                'first_name' => $order->customer_name ?: 'Pelanggan',
                'email'      => $order->customer_email,
                'phone'      => $order->customer_whatsapp ?: '',
            ],
        ];

        return Snap::getSnapToken($params);
    }

    public function clientKey(): string
    {
        return (string) config('services.midtrans.client_key');
    }

    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production');
    }

    public function snapJsUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Verifikasi signature webhook Midtrans.
     */
    public function isValidSignature(array $payload): bool
    {
        $orderId    = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey  = (string) config('services.midtrans.server_key');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return isset($payload['signature_key']) && hash_equals($expected, (string) $payload['signature_key']);
    }

    /**
     * Terjemahkan status transaksi Midtrans -> [order_status, payment_status].
     *
     * @return array{0:string,1:string}
     */
    public function mapStatus(array $payload): array
    {
        $trx   = $payload['transaction_status'] ?? '';
        $fraud = $payload['fraud_status'] ?? 'accept';

        return match ($trx) {
            'capture'    => $fraud === 'challenge' ? ['pending', 'challenge'] : ['paid', 'paid'],
            'settlement' => ['paid', 'paid'],
            'pending'    => ['pending', 'pending'],
            'deny'       => ['failed', 'failed'],
            'cancel'     => ['cancelled', 'failed'],
            'expire'     => ['expired', 'expired'],
            'refund', 'partial_refund' => ['cancelled', 'failed'],
            default      => ['pending', 'pending'],
        };
    }
}
