<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBrand extends Model
{
    /** Flush cache landing tiap ada perubahan katalog. */
    protected static function booted(): void
    {
        $flush = fn () => \Illuminate\Support\Facades\Cache::forget(\App\Http\Controllers\Frontend\LandingController::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    protected $fillable = [
        'name', 'slug', 'tab', 'category', 'logo',
        'input_label', 'note', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Daftar tab yang tersedia (label untuk UI). */
    public const TABS = [
        'pulsa_ppob'     => 'Pulsa & PPOB',
        'game_streaming' => 'Game & Streaming',
        'sosmed'         => 'Sosmed',
    ];

    /** Kategori (menentukan skema margin di config/margin.php). */
    public const CATEGORIES = [
        'pulsa'     => 'Pulsa',
        'data'      => 'Paket Data',
        'pln'       => 'Token PLN',
        'ewallet'   => 'E-Money',
        'game'      => 'Game',
        'voucher'   => 'Voucher',
        'streaming' => 'Streaming',
        'sosmed'    => 'Sosmed',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTabLabelAttribute(): string
    {
        return self::TABS[$this->tab] ?? $this->tab;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
