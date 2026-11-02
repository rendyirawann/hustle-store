<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('product_brands')->cascadeOnDelete();
            $table->string('code')->unique();       // e.g. "AX5", "MLGLOBAL13-S14"
            $table->string('name');                 // e.g. "Axis 5.000", "86 Diamonds (78 + 8 Bonus)"
            $table->string('note')->nullable();     // e.g. "Reguler", "masukkan nomor meter/id pelanggan"

            // Modal / cost = the price you pay to the upstream provider (VIPAYMENT).
            $table->unsignedBigInteger('cost_price')->default(0);
            // Your own selling prices (auto-computed from cost via the margin engine, editable).
            $table->unsignedBigInteger('price')->default(0);          // Member / retail
            $table->unsignedBigInteger('reseller_price')->default(0); // Reseller
            $table->unsignedBigInteger('h2h_price')->nullable();      // Host-to-Host / API

            // Availability from the provider: available | empty | cutoff
            $table->string('status')->default('available');
            // Your own on/off switch (whether the product is sellable/shown).
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('brand_id');
            $table->index('is_active');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
