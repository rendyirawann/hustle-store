<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();   // dipakai juga sbg Midtrans order_id

            // Snapshot produk (agar histori tetap utuh walau produk diubah/dihapus)
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('brand_name')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();

            // Data tujuan & pembeli (guest checkout)
            $table->string('target');                    // ID game / no HP / ID pelanggan
            $table->string('target_label')->nullable();  // label input saat order
            $table->string('customer_name')->nullable();
            $table->string('customer_email');
            $table->string('customer_whatsapp')->nullable();

            // Uang
            $table->unsignedBigInteger('cost_price')->default(0); // modal (untuk hitung profit)
            $table->unsignedBigInteger('amount');                 // yang dibayar pelanggan (harga jual)

            // Status pesanan (fulfillment): pending|paid|processing|success|failed|expired|cancelled
            $table->string('status')->default('pending');
            // Status pembayaran: pending|paid|failed|expired|challenge
            $table->string('payment_status')->default('pending');

            // Data Midtrans
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('va_number')->nullable();
            $table->json('payment_payload')->nullable();

            // Fulfillment manual (diisi admin setelah top up di VIP Reseller)
            $table->text('serial_number')->nullable(); // SN / kode / catatan hasil top up
            $table->text('admin_note')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->string('ip')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
