<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    /**
     * Key cache data landing. Di-flush otomatis saat Product/ProductBrand
     * berubah (lihat booted() di kedua model).
     */
    public const CACHE_KEY = 'landing.brands.v1';

    public function index()
    {
        // Cache 30 menit — query katalog tidak dijalankan di tiap kunjungan.
        $brands = Cache::remember(self::CACHE_KEY, 1800, function () {
            return ProductBrand::query()
                ->where('is_active', true)
                ->withCount(['products as active_products_count' => fn ($q) => $q->where('is_active', true)])
                ->withMin(['products as min_price' => fn ($q) => $q->where('is_active', true)], 'price')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->filter(fn ($b) => $b->active_products_count > 0)
                ->values();
        });

        $byCategory = $brands->groupBy('category');

        return view('frontend.landing', [
            'brandsByCategory' => $byCategory,
            'totalProducts'    => $brands->sum('active_products_count'),
            'totalBrands'      => $brands->count(),
        ]);
    }
}
