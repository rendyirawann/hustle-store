<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Menyinkronkan katalog HustleStore (product_brands + products) dengan daftar
 * layanan VIPayment. Murni olah data — pengambilan dari API ada di command.
 *
 *  - Produk yang kodenya ada di VIP  → cost_price & status diperbarui. Harga jual
 *    dihitung ulang dari margin HANYA jika modal berubah.
 *  - Produk yang kodenya hilang di VIP → status "cutoff" + dinonaktifkan.
 *  - (opsional) Produk VIP yang belum ada → diimpor (hanya yang "available").
 *    Masuk ke brand yang sudah ada bila cocok; selain itu dibuatkan brand baru
 *    yang NONAKTIF supaya tidak langsung tampil di toko.
 */
class VipaymentCatalogSync
{
    /** Tipe prepaid VIP → [kategori margin, tab, akhiran nama brand, label input]. */
    private const PREPAID_TYPES = [
        'pulsa-reguler'       => ['pulsa', 'pulsa_ppob', '', 'Nomor HP'],
        'pulsa-transfer'      => ['pulsa', 'pulsa_ppob', 'Transfer Pulsa', 'Nomor HP'],
        'pulsa-internasional' => ['pulsa', 'pulsa_ppob', 'Pulsa Internasional', 'Nomor HP'],
        'paket-internet'      => ['data', 'pulsa_ppob', 'Paket Data', 'Nomor HP'],
        'paket-telepon'       => ['data', 'pulsa_ppob', 'Telepon & SMS', 'Nomor HP'],
        'paket-lainnya'       => ['data', 'pulsa_ppob', 'Paket Lainnya', 'Nomor HP'],
        'token-pln'           => ['pln', 'pulsa_ppob', '', 'Nomor Meter / ID Pelanggan'],
        'saldo-emoney'        => ['ewallet', 'pulsa_ppob', '', 'Nomor HP / ID Akun'],
        'streaming-tv'        => ['streaming', 'pulsa_ppob', '', 'Nomor Pelanggan'],
        'voucher-game'        => ['voucher', 'game_streaming', '', 'Nomor HP / ID'],
    ];

    /** Kata kunci nama "game" VIP yang sebenarnya aplikasi premium/streaming. */
    private const STREAMING_HINTS = [
        'premium', ' pro', 'prime video', 'premier', 'netflix', 'spotify', 'youtube', 'disney',
        'chatgpt', 'gemini', 'perplexity', 'alight motion', 'vision plus', 'rcti', 'tiktok music', 'warp plus',
    ];

    /** Kata kunci nama "game" VIP yang berupa voucher / gift card. */
    private const VOUCHER_HINTS = [
        'voucher', 'gift card', 'wallet code', 'google play', 'playstation', 'garena shell', 'point card',
    ];

    /** Batas minimum jumlah item agar penandaan "hilang dari VIP" dianggap aman. */
    private const MIN_ITEMS_FOR_MISSING = 100;

    public function __construct(private readonly string $tier = 'basic') {}

    /**
     * @param  array  $prepaid  data mentah /api/prepaid type=services
     * @param  array  $game     data mentah /api/game-feature type=services
     */
    public function run(array $prepaid, array $game, bool $importNew = false, bool $dryRun = false): array
    {
        $items = $this->normalize($prepaid, $game);
        $canMarkMissing = count($prepaid) >= self::MIN_ITEMS_FOR_MISSING
            && count($game) >= self::MIN_ITEMS_FOR_MISSING;

        $report = [
            'vip_items'     => count($items),
            'matched'       => 0,
            'price_changes' => [],
            'status_changes'=> 0,
            'missing'       => [],
            'relisted'      => [],
            'import'        => [],   // per grup: [group, brand, is_new_brand, count]
            'imported'      => 0,
            'new_brands'    => 0,
        ];

        $work = function () use ($items, $canMarkMissing, $importNew, $dryRun, &$report) {
            $votes = $this->syncExisting($items, $canMarkMissing, $dryRun, $report);

            if ($importNew) {
                $this->importNew($items, $votes, $dryRun, $report);
            }
        };

        $dryRun ? $work() : DB::transaction($work);

        return $report;
    }

    /** @return array<string, array<int,int>>  grup VIP → [brand_id => jumlah produk cocok] */
    private function syncExisting(array $items, bool $canMarkMissing, bool $dryRun, array &$report): array
    {
        $votes = [];

        foreach (Product::with('brand')->orderBy('id')->get() as $product) {
            $vip = $items[$product->code] ?? null;

            if (! $vip) {
                if ($canMarkMissing && ($product->status !== 'cutoff' || $product->is_active)) {
                    $report['missing'][] = [$product->code, $product->name, $product->brand?->name];
                    $product->status = 'cutoff';
                    $product->is_active = false;
                    $dryRun || $product->save();
                }
                continue;
            }

            $report['matched']++;
            $votes[$vip['group']][$product->brand_id] = ($votes[$vip['group']][$product->brand_id] ?? 0) + 1;

            if ($vip['cost'] > 0 && $vip['cost'] !== (int) $product->cost_price) {
                $old = ['cost' => (int) $product->cost_price, 'price' => (int) $product->price];
                $product->cost_price = $vip['cost'];
                $product->applyMargin();
                $report['price_changes'][] = [
                    $product->code, $product->name,
                    $old['cost'], $vip['cost'], $old['price'], (int) $product->price,
                ];
            }

            $status = $vip['cost'] > 0 ? $vip['status'] : 'empty';
            if ($product->status !== $status) {
                if ($product->status === 'cutoff') {
                    $report['relisted'][] = [$product->code, $product->name, $product->is_active ? 'aktif' : 'NONAKTIF'];
                }
                $product->status = $status;
                $report['status_changes']++;
            }

            // Bersihkan catatan "Perkiraan harga" dari seed lama — harga kini harga asli VIP.
            if ($product->note === 'Perkiraan harga') {
                $product->note = $vip['note'];
            }

            if ($product->isDirty() && ! $dryRun) {
                $product->save();
            }
        }

        return $votes;
    }

    private function importNew(array $items, array $votes, bool $dryRun, array &$report): void
    {
        $known = array_flip(Product::pluck('code')->all());
        $brandsBySlug = ProductBrand::all()->keyBy('slug');
        $nextBrandSort = (int) ProductBrand::max('sort_order') + 1;

        // Grup VIP → brand lokal (pemenang voting dari produk yang sudah cocok).
        $groupBrand = [];
        foreach ($votes as $group => $byBrand) {
            arsort($byBrand);
            $groupBrand[$group] = array_key_first($byBrand);
        }

        $candidates = [];
        foreach ($items as $code => $row) {
            if (! isset($known[$code]) && $row['status'] === 'available' && $row['cost'] > 0) {
                $candidates[$row['group']][] = $row;
            }
        }
        ksort($candidates);

        foreach ($candidates as $group => $rows) {
            $meta = $this->brandMeta($rows[0]);
            $brand = isset($groupBrand[$group])
                ? $brandsBySlug->firstWhere('id', $groupBrand[$group])
                : $brandsBySlug->get($meta['slug']);
            $isNewBrand = ! $brand;

            if ($isNewBrand) {
                $brand = new ProductBrand($meta + ['sort_order' => $nextBrandSort++, 'is_active' => false]);
                if (! $dryRun) {
                    $brand->save();
                }
                $brandsBySlug->put($meta['slug'], $brand);
                $report['new_brands']++;
            }

            $report['import'][] = [$group, $brand->name, $isNewBrand, count($rows)];
            $report['imported'] += count($rows);

            if ($dryRun) {
                continue;
            }

            usort($rows, fn ($a, $b) => $a['cost'] <=> $b['cost']);
            $sort = (int) Product::where('brand_id', $brand->id)->max('sort_order') + 1;

            foreach ($rows as $row) {
                $product = new Product([
                    'brand_id'   => $brand->id,
                    'code'       => $row['code'],
                    'name'       => Str::limit($row['name'], 250),
                    'note'       => $row['note'],
                    'cost_price' => $row['cost'],
                    'status'     => 'available',
                    'is_active'  => true,
                    'sort_order' => $sort++,
                ]);
                $product->applyMargin($brand->category)->save();
            }
        }
    }

    /** Atribut brand baru untuk sebuah item VIP. */
    private function brandMeta(array $row): array
    {
        if ($row['source'] === 'prepaid') {
            [$category, $tab, $suffix, $label] = self::PREPAID_TYPES[$row['type']];
            $name = trim($row['brand'] . ' ' . $suffix);
        } else {
            $name = $row['group'];
            $lower = ' ' . Str::lower($name);
            $category = Str::contains($lower, self::VOUCHER_HINTS) ? 'voucher'
                : (Str::contains($lower, self::STREAMING_HINTS) ? 'streaming' : 'game');
            $tab = 'game_streaming';
            $label = match (true) {
                $category !== 'game'                                       => 'Email / Nomor HP',
                Str::contains($lower, ['mobile legends', 'magic chess'])   => 'User ID (Zone ID)',
                default                                                    => 'User ID',
            };
        }

        $slug = Str::slug($name) ?: 'vip-' . substr(md5($name), 0, 8);

        return [
            'name'        => $name,
            'slug'        => $slug,
            'tab'         => $tab,
            'category'    => $category,
            'input_label' => $label,
        ];
    }

    /**
     * Satukan data prepaid + game menjadi [kode => baris ternormalisasi].
     * Kode yang muncul lebih dari sekali (mis. Mobile Legends A & B) diambil
     * yang tersedia, lalu yang modalnya paling tinggi (aman untuk margin).
     */
    private function normalize(array $prepaid, array $game): array
    {
        $items = [];

        foreach ($prepaid as $r) {
            $type = (string) ($r['type'] ?? '');
            if (empty($r['code']) || ! isset(self::PREPAID_TYPES[$type])) {
                continue;
            }
            $brand = trim((string) ($r['brand'] ?? ''));
            $this->put($items, [
                'source' => 'prepaid',
                'code'   => (string) $r['code'],
                'name'   => trim((string) ($r['name'] ?? $r['code'])),
                'note'   => $this->cleanNote($r['note'] ?? null),
                'cost'   => $this->cost($r['price'] ?? []),
                'status' => $this->status($r['status'] ?? ''),
                'group'  => $brand . '|' . $type,
                'brand'  => $brand,
                'type'   => $type,
            ]);
        }

        foreach ($game as $r) {
            if (empty($r['code'])) {
                continue;
            }
            $this->put($items, [
                'source' => 'game',
                'code'   => (string) $r['code'],
                'name'   => trim((string) ($r['name'] ?? $r['code'])),
                'note'   => $this->cleanNote($r['description'] ?? null),
                'cost'   => $this->cost($r['price'] ?? []),
                'status' => $this->status($r['status'] ?? ''),
                'group'  => trim((string) ($r['game'] ?? 'Lainnya')),
                'brand'  => null,
                'type'   => null,
            ]);
        }

        return $items;
    }

    private function put(array &$items, array $row): void
    {
        $existing = $items[$row['code']] ?? null;

        if ($existing) {
            $moreAvailable = $row['status'] === 'available' && $existing['status'] !== 'available';
            $sameButPricier = $row['status'] === $existing['status'] && $row['cost'] > $existing['cost'];
            if (! $moreAvailable && ! $sameButPricier) {
                return;
            }
        }

        $items[$row['code']] = $row;
    }

    private function cost(array $price): int
    {
        return (int) ($price[$this->tier] ?? $price['basic'] ?? 0);
    }

    private function status(string $vipStatus): string
    {
        return $vipStatus === 'available' ? 'available' : 'empty';
    }

    private function cleanNote(?string $note): ?string
    {
        $text = self::plainText($note);

        return ($text === '' || $text === '-') ? null : Str::limit($text, 250);
    }

    /** HTML → teks satu baris (tag diganti spasi agar kata tidak menempel). */
    public static function plainText(?string $html): string
    {
        $text = html_entity_decode(strip_tags(preg_replace('/<[^>]*>/', ' $0', (string) $html)));

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
