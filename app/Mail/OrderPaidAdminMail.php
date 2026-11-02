<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Pesanan LUNAS ' . $this->order->order_number . ' — siap diproses',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-paid-admin', with: ['order' => $this->order]);
    }
}
