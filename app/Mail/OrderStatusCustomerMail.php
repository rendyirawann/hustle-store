<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param string $type received|success|failed */
    public function __construct(public Order $order, public string $type = 'received') {}

    public function envelope(): Envelope
    {
        $no = $this->order->order_number;
        $subject = match ($this->type) {
            'success' => '✅ Pesanan ' . $no . ' Berhasil',
            'failed'  => '⚠️ Pesanan ' . $no . ' Gagal',
            default   => '🧾 Pembayaran Diterima — Pesanan ' . $no,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-status-customer', with: [
            'order' => $this->order,
            'type'  => $this->type,
        ]);
    }
}
