<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::withCount('products')
            ->orderBy('tab')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('tab');

        return view('backend.product.brand', [
            'brandsByTab' => $brands,
            'tabs'        => ProductBrand::TABS,
            'categories'  => ProductBrand::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        $brand = ProductBrand::create($data);

        activity()->useLog('brand')->causedBy(auth()->user())
            ->performedOn($brand)->log('Menambah brand ' . $brand->name);

        return back()->with('success', 'Brand "' . $brand->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, ProductBrand $brand)
    {
        $data = $this->validated($request, $brand->id);
        $data['is_active'] = $request->boolean('is_active', $brand->is_active);

        // Ubah slug hanya jika nama berubah.
        if ($data['name'] !== $brand->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $brand->id);
        }

        $brand->update($data);

        activity()->useLog('brand')->causedBy(auth()->user())
            ->performedOn($brand)->log('Mengubah brand ' . $brand->name);

        return back()->with('success', 'Brand "' . $brand->name . '" berhasil diperbarui.');
    }

    public function toggle(ProductBrand $brand)
    {
        $brand->update(['is_active' => ! $brand->is_active]);

        return back()->with('success', 'Brand "' . $brand->name . '" kini ' . ($brand->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    public function destroy(ProductBrand $brand)
    {
        $name = $brand->name;
        $brand->delete(); // products ikut terhapus (cascade)

        activity()->useLog('brand')->causedBy(auth()->user())->log('Menghapus brand ' . $name);

        return back()->with('success', 'Brand "' . $name . '" beserta produknya berhasil dihapus.');
    }

    private function validated(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name'        => 'required|string|max:150',
            'tab'         => 'required|in:' . implode(',', array_keys(ProductBrand::TABS)),
            'category'    => 'required|in:' . implode(',', array_keys(ProductBrand::CATEGORIES)),
            'input_label' => 'nullable|string|max:150',
            'note'        => 'nullable|string|max:500',
            'logo'        => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
        ], [], [
            'name' => 'Nama brand',
        ]) + ['input_label' => $request->input('input_label') ?: 'Nomor Tujuan', 'sort_order' => (int) $request->input('sort_order', 0)];
    }

    private function uniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'brand';
        $slug = $base;
        $i = 2;
        while (ProductBrand::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
