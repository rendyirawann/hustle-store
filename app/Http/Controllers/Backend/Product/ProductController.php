<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Services\PriceCalculator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $brands = ProductBrand::orderBy('tab')->orderBy('sort_order')->orderBy('name')->get();

        // Brand terpilih (dari query ?brand=slug), default brand pertama.
        $currentBrand = null;
        if ($request->filled('brand')) {
            $currentBrand = $brands->firstWhere('slug', $request->brand);
        }
        $currentBrand = $currentBrand ?? $brands->first();

        $products = collect();
        if ($currentBrand) {
            $query = Product::where('brand_id', $currentBrand->id);

            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'ILIKE', "%{$q}%")->orWhere('code', 'ILIKE', "%{$q}%");
                });
            }

            $products = $query->orderBy('sort_order')->orderBy('cost_price')->get();
        }

        return view('backend.product.index', [
            'brands'       => $brands,
            'brandsByTab'  => $brands->groupBy('tab'),
            'currentBrand' => $currentBrand,
            'products'     => $products,
            'tabs'         => ProductBrand::TABS,
            'statuses'     => Product::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $brand = ProductBrand::findOrFail($data['brand_id']);

        $product = new Product($this->fill($request, $brand, $data));
        $product->save();

        activity()->useLog('product')->causedBy(auth()->user())
            ->performedOn($product)->log('Menambah produk ' . $product->code);

        return redirect()->route('products.index', ['brand' => $brand->slug])
            ->with('success', 'Produk "' . $product->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);
        $brand = ProductBrand::findOrFail($data['brand_id']);

        $product->fill($this->fill($request, $brand, $data));
        $product->save();

        activity()->useLog('product')->causedBy(auth()->user())
            ->performedOn($product)->log('Mengubah produk ' . $product->code);

        return redirect()->route('products.index', ['brand' => $brand->slug])
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui.');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', 'Produk "' . $product->name . '" kini ' . ($product->is_active ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    public function destroy(Product $product)
    {
        $brandSlug = $product->brand?->slug;
        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index', ['brand' => $brandSlug])
            ->with('success', 'Produk "' . $name . '" berhasil dihapus.');
    }

    /**
     * Regenerasi harga jual seluruh produk (atau satu brand) dari harga modal
     * memakai skema margin di config/margin.php.
     */
    public function regenerate(Request $request)
    {
        $brand = null;
        if ($request->filled('brand_id')) {
            $brand = ProductBrand::find($request->brand_id);
        }

        $query = Product::query()->with('brand');
        if ($brand) {
            $query->where('brand_id', $brand->id);
        }

        $count = 0;
        $query->chunkById(200, function ($chunk) use (&$count) {
            foreach ($chunk as $product) {
                $product->applyMargin()->save();
                $count++;
            }
        });

        $scope = $brand ? ('brand ' . $brand->name) : 'semua produk';
        return back()->with('success', "Harga {$scope} berhasil diregenerasi dari margin ({$count} produk).");
    }

    private function validated(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'brand_id'       => 'required|exists:product_brands,id',
            'code'           => 'required|string|max:100|unique:products,code' . ($ignoreId ? ',' . $ignoreId : ''),
            'name'           => 'required|string|max:255',
            'note'           => 'nullable|string|max:255',
            'cost_price'     => 'required|integer|min:0',
            'price'          => 'nullable|integer|min:0',
            'reseller_price' => 'nullable|integer|min:0',
            'h2h_price'      => 'nullable|integer|min:0',
            'status'         => 'required|in:' . implode(',', array_keys(Product::STATUSES)),
            'sort_order'     => 'nullable|integer|min:0',
        ], [], [
            'code' => 'Kode', 'name' => 'Nama produk', 'cost_price' => 'Harga modal',
        ]);
    }

    /**
     * Susun atribut produk. Harga jual kosong akan diisi otomatis dari margin.
     */
    private function fill(Request $request, ProductBrand $brand, array $data): array
    {
        $cost = (int) $data['cost_price'];
        $auto = PriceCalculator::all($cost, $brand->category);

        return [
            'brand_id'       => $brand->id,
            'code'           => $data['code'],
            'name'           => $data['name'],
            'note'           => $data['note'] ?? null,
            'cost_price'     => $cost,
            'price'          => $request->filled('price') ? (int) $data['price'] : $auto['price'],
            'reseller_price' => $request->filled('reseller_price') ? (int) $data['reseller_price'] : $auto['reseller_price'],
            'h2h_price'      => $request->filled('h2h_price') ? (int) $data['h2h_price'] : $auto['h2h_price'],
            'status'         => $data['status'],
            'is_active'      => $request->boolean('is_active', true),
            'sort_order'     => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
