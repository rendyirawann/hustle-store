<?php

namespace App\Models;

use App\Services\PriceCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** Flush cache landing tiap ada perubahan katalog. */
    protected static function booted(): void
    {
        $flush = fn () => \Illuminate\Support\Facades\Cache::forget(\App\Http\Controllers\Frontend\LandingController::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    protected $fillable = [
        'brand_id', 'code', 'name', 'note',
        'cost_price', 'price', 'reseller_price', 'h2h_price',
        'status', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'cost_price'     => 'integer',
        'price'          => 'integer',
        'reseller_price' => 'integer',
        'h2h_price'      => 'integer',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
    ];

    /** Status ketersediaan dari provider. */
    public const STATUSES = [
        'available' => 'Tersedia',
        'empty'     => 'Empty',
        'cutoff'    => 'Cut Off',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Hitung ulang harga jual dari cost_price memakai kategori brand.
     * (Tidak menyimpan — panggil ->save() setelahnya bila perlu.)
     */
    public function applyMargin(?string $category = null): self
    {
        $category = $category ?? $this->brand?->category ?? 'game';
        $prices = PriceCalculator::all((int) $this->cost_price, $category);
        $this->price          = $prices['price'];
        $this->reseller_price = $prices['reseller_price'];
        $this->h2h_price      = $prices['h2h_price'];

        return $this;
    }

    /** Keuntungan (Rp) tier member dibanding modal. */
    public function getProfitAttribute(): int
    {
        return (int) $this->price - (int) $this->cost_price;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
