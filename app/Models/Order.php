<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'product_id', 'brand_name', 'product_code', 'product_name',
        'target', 'target_label', 'customer_name', 'customer_email', 'customer_whatsapp',
        'cost_price', 'amount', 'status', 'payment_status',
        'snap_token', 'payment_type', 'midtrans_transaction_id', 'va_number', 'payment_payload',
        'serial_number', 'admin_note', 'paid_at', 'processed_at', 'completed_at', 'ip',
    ];

    protected $casts = [
        'cost_price'      => 'integer',
        'amount'          => 'integer',
        'payment_payload' => 'array',
        'paid_at'         => 'datetime',
        'processed_at'    => 'datetime',
        'completed_at'    => 'datetime',
    ];

    public const STATUSES = [
        'pending'    => 'Menunggu Pembayaran',
        'paid'       => 'Lunas — Menunggu Diproses',
        'processing' => 'Sedang Diproses',
        'success'    => 'Berhasil',
        'failed'     => 'Gagal',
        'expired'    => 'Kedaluwarsa',
        'cancelled'  => 'Dibatalkan',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'HS-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getProfitAttribute(): int
    {
        return (int) $this->amount - (int) $this->cost_price;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'success'], true);
    }
}
