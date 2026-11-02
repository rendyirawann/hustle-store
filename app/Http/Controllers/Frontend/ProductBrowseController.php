<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;

class ProductBrowseController extends Controller
{
    /** Katalog semua brand aktif. */
    public function index()
    {
        $brands = ProductBrand::query()
            ->where('is_active', true)
            ->withCount(['products as active_products_count' => fn ($q) => $q->where('is_active', true)])
            ->withMin(['products as min_price' => fn ($q) => $q->where('is_active', true)], 'price')
            ->orderBy('sort_order')->orderBy('name')
            ->get()
            ->filter(fn ($b) => $b->active_products_count > 0)
            ->values()
            ->groupBy('category');

        return view('frontend.catalog', ['brandsByCategory' => $brands]);
    }

    /** Halaman satu brand: seluruh produk + form order. */
    public function show(string $slug)
    {
        $brand = ProductBrand::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $products = $brand->products()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        abort_if($products->isEmpty(), 404);

        return view('frontend.brand', [
            'brand'    => $brand,
            'products' => $products,
        ]);
    }
}
