<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g. "Telkomsel", "Mobile Legends (Global)", "Netflix"
            $table->string('slug')->unique();
            // High-level tab shown in the UI: pulsa_ppob | game_streaming | sosmed
            $table->string('tab')->default('pulsa_ppob');
            // Pricing/behaviour category used by the margin engine:
            // pulsa | data | pln | ewallet | game | voucher | streaming | sosmed
            $table->string('category')->default('game');
            $table->string('logo')->nullable();
            $table->string('input_label')->default('Nomor Tujuan'); // hint for the order form
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tab', 'category']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_brands');
    }
};
