<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderPaidAdminMail;
use App\Mail\OrderStatusCustomerMail;
use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    /** Buat pesanan + Snap token, lalu arahkan ke halaman pembayaran. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'        => 'required|exists:products,id',
            'target'            => 'required|string|max:150',
            'customer_name'     => 'nullable|string|max:120',
            'customer_email'    => 'required|email|max:150',
            'customer_whatsapp' => 'nullable|string|max:25',
            'agree'             => 'accepted',
        ], [
            'agree.accepted' => 'Anda harus menyetujui Syarat & Ketentuan serta Kebijakan Privasi sebelum melanjutkan.',
        ], [
            'target'         => 'data tujuan',
            'customer_email' => 'email',
        ]);

        $product = Product::with('brand')->findOrFail($data['product_id']);

        // Pastikan produk & brand aktif dan tersedia.
        if (! $product->is_active || ! $product->brand || ! $product->brand->is_active) {
            return back()->with('order_error', 'Produk tidak tersedia.')->withInput();
        }
        if ($product->status !== 'available') {
            return back()->with('order_error', 'Maaf, produk ini sedang tidak tersedia (' . $product->status_label . ').')->withInput();
        }

        $order = Order::create([
            'order_number'      => Order::generateNumber(),
            'product_id'        => $product->id,
            'brand_name'        => $product->brand->name,
            'product_code'      => $product->code,
            'product_name'      => $product->name,
            'target'            => $data['target'],
            'target_label'      => $product->brand->input_label,
            'customer_name'     => $data['customer_name'] ?? null,
            'customer_email'    => $data['customer_email'],
            'customer_whatsapp' => $data['customer_whatsapp'] ?? null,
            'cost_price'        => $product->cost_price,
            'amount'            => $product->price,
            'status'            => 'pending',
            'payment_status'    => 'pending',
            'ip'                => $request->ip(),
        ]);

        try {
            $order->snap_token = $this->midtrans->createSnapToken($order);
            $order->save();
        } catch (\Throwable $e) {
            Log::error('Midtrans snap token gagal: ' . $e->getMessage());
            $order->update(['status' => 'failed', 'payment_status' => 'failed']);
            return back()->with('order_error', 'Gagal membuat transaksi pembayaran. Coba lagi.')->withInput();
        }

        return redirect()->route('order.pay', $order->order_number);
    }

    /** Halaman Snap pembayaran. */
    public function pay(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Jika sudah dibayar, langsung ke status.
        if ($order->isPaid()) {
            return redirect()->route('order.status', $order->order_number);
        }
        if (! $order->snap_token) {
            abort(404);
        }

        return view('frontend.pay', [
            'order'     => $order,
            'snapToken' => $order->snap_token,
            'clientKey' => $this->midtrans->clientKey(),
            'snapJs'    => $this->midtrans->snapJsUrl(),
        ]);
    }

    /** Halaman status pesanan (bisa diakses tamu via nomor order). */
    public function status(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('frontend.order-status', ['order' => $order]);
    }

    /** Form "lacak pesanan". */
    public function track(Request $request)
    {
        if ($request->filled('order_number')) {
            $order = Order::where('order_number', trim($request->order_number))->first();
            if ($order) {
                return redirect()->route('order.status', $order->order_number);
            }
            return back()->with('order_error', 'Nomor pesanan tidak ditemukan.');
        }

        return view('frontend.order-track');
    }

    /** Webhook notifikasi Midtrans (tanpa CSRF, dikecualikan di bootstrap/app.php). */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (! $this->midtrans->isValidSignature($payload)) {
            Log::warning('Midtrans webhook signature tidak valid', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $order = Order::where('order_number', $payload['order_id'] ?? '')->first();
        if (! $order) {
            return response()->json(['message' => 'order not found'], 404);
        }

        [$newStatus, $newPayment] = $this->midtrans->mapStatus($payload);
        $wasPaid = $order->isPaid();

        $order->payment_type            = $payload['payment_type'] ?? $order->payment_type;
        $order->midtrans_transaction_id = $payload['transaction_id'] ?? $order->midtrans_transaction_id;
        $order->va_number               = $payload['va_numbers'][0]['va_number'] ?? ($payload['permata_va_number'] ?? $order->va_number);
        $order->payment_payload         = $payload;
        $order->payment_status          = $newPayment;

        // Jangan mundurkan status fulfillment kalau admin sudah memproses.
        if (! in_array($order->status, ['processing', 'success'], true)) {
            $order->status = $newStatus;
        }

        if ($newPayment === 'paid' && ! $order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        // Kirim email hanya saat transisi pertama kali menjadi LUNAS.
        if ($newPayment === 'paid' && ! $wasPaid) {
            $this->sendPaidEmails($order);
        }

        return response()->json(['message' => 'ok']);
    }

    private function sendPaidEmails(Order $order): void
    {
        $adminEmail = config('services.midtrans.admin_order_email') ?: config('mail.from.address');

        try {
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new OrderPaidAdminMail($order));
            }
            Mail::to($order->customer_email)->send(new OrderStatusCustomerMail($order, 'received'));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email order ' . $order->order_number . ': ' . $e->getMessage());
        }
    }
}
