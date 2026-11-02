<?php

namespace App\Console\Commands;

use App\Services\VipaymentCatalogSync;
use App\Services\VipaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VipaymentSync extends Command
{
    protected $signature = 'vip:sync
        {--dry-run : Tampilkan perubahan tanpa menyimpan ke database}
        {--import-new : Impor juga produk VIP yang belum ada di katalog (hanya yang tersedia)}
        {--with-sosmed : Ikut ambil layanan Sosial Media (hanya untuk file CSV, tidak diimpor)}';

    protected $description = 'Sinkron harga modal & status produk dari API VIPayment (vip-reseller.co.id)';

    public function handle(): int
    {
        try {
            $vip = VipaymentService::fromConfig();

            $this->info('Mengambil daftar layanan dari VIPayment…');
            $prepaid = $vip->prepaidServices();
            $game    = $vip->gameServices();
            $sosmed  = [];
            if ($this->option('with-sosmed')) {
                try {
                    $sosmed = $vip->socialMediaServices();
                } catch (Throwable $e) {
                    $this->warn('Sosmed dilewati: ' . $e->getMessage());
                }
            }
            $this->line(sprintf('  Pulsa & PPOB: %d | Game & Streaming: %d%s',
                count($prepaid), count($game), $sosmed ? ' | Sosmed: ' . count($sosmed) : ''));

            $csvPath = $this->saveSnapshot($prepaid, $game, $sosmed);
            $this->line("  Snapshot daftar harga: storage/app/private/{$csvPath}");

            $tier = (string) config('services.vipayment.price_tier', 'basic');
            $dry  = (bool) $this->option('dry-run');

            $report = (new VipaymentCatalogSync($tier))
                ->run($prepaid, $game, (bool) $this->option('import-new'), $dry);
        } catch (Throwable $e) {
            Log::warning('vip:sync gagal: ' . $e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->printReport($report, $dry, $tier);

        return self::SUCCESS;
    }

    private function printReport(array $r, bool $dry, string $tier): void
    {
        $this->newLine();
        $this->info(($dry ? '[DRY RUN — tidak ada yang disimpan] ' : '') . "Ringkasan (harga modal = kolom '{$tier}'):");
        $this->table(['Item', 'Jumlah'], [
            ['Produk katalog yang cocok dengan VIP', $r['matched']],
            ['Harga modal berubah (harga jual dihitung ulang)', count($r['price_changes'])],
            ['Status berubah (available/empty)', $r['status_changes']],
            ['Hilang dari VIP → Cut Off + nonaktif', count($r['missing'])],
            ['Kembali dijual VIP', count($r['relisted'])],
            ['Produk baru diimpor', $r['imported']],
            ['Brand baru (nonaktif)', $r['new_brands']],
        ]);

        if ($r['price_changes']) {
            $rows = array_map(fn ($c) => [
                $c[0], mb_strimwidth($c[1], 0, 34, '…'),
                $this->rp($c[2]), $this->rp($c[3]), $this->rp($c[4]), $this->rp($c[5]),
            ], $r['price_changes']);
            $limit = $this->output->isVerbose() ? count($rows) : 25;
            $this->table(['Kode', 'Produk', 'Modal lama', 'Modal baru', 'Jual lama', 'Jual baru'], array_slice($rows, 0, $limit));
            if (count($rows) > $limit) {
                $this->line('  … dan ' . (count($rows) - $limit) . ' lainnya (pakai -v untuk semua).');
            }
        }

        if ($r['missing']) {
            $this->warn('Tidak ada lagi di VIP (dinonaktifkan):');
            $this->table(['Kode', 'Produk', 'Brand'], $r['missing']);
        }

        if ($r['relisted']) {
            $this->info('Kembali tersedia di VIP (cek toggle aktif di admin):');
            $this->table(['Kode', 'Produk', 'Aktif?'], $r['relisted']);
        }

        if ($r['import']) {
            $rows = array_map(fn ($g) => [$g[0], $g[1], $g[2] ? 'BARU (nonaktif)' : 'sudah ada', $g[3]], $r['import']);
            $this->table(['Grup VIP', 'Masuk ke brand', 'Brand', 'Produk'], $rows);
        }
    }

    /** Simpan data mentah (JSON) + CSV daftar harga lengkap ke storage/app/private/vipayment. */
    private function saveSnapshot(array $prepaid, array $game, array $sosmed): string
    {
        $disk = Storage::disk('local');
        $json = fn ($d) => json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $disk->put('vipayment/prepaid.json', $json($prepaid));
        $disk->put('vipayment/game.json', $json($game));
        if ($sosmed) {
            $disk->put('vipayment/sosmed.json', $json($sosmed));
        }

        $fh = fopen('php://temp', 'w+');
        fwrite($fh, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
        fputcsv($fh, ['sumber', 'grup', 'kategori', 'kode', 'nama', 'catatan', 'harga_basic', 'harga_premium', 'harga_special', 'status']);
        foreach ($prepaid as $i) {
            fputcsv($fh, ['Pulsa & PPOB', $i['brand'] ?? '', trim(($i['type'] ?? '') . ' / ' . ($i['category'] ?? ''), ' /'),
                $i['code'] ?? '', $i['name'] ?? '', $i['note'] ?? '', ...$this->prices($i), $i['status'] ?? '']);
        }
        foreach ($game as $i) {
            fputcsv($fh, ['Game & Streaming', $i['game'] ?? '', 'server ' . ($i['server'] ?? ''),
                $i['code'] ?? '', $i['name'] ?? '', VipaymentCatalogSync::plainText($i['description'] ?? null), ...$this->prices($i), $i['status'] ?? '']);
        }
        foreach ($sosmed as $i) {
            fputcsv($fh, ['Sosmed', $i['category'] ?? '', 'min ' . ($i['min'] ?? '') . ' / max ' . ($i['max'] ?? ''),
                $i['id'] ?? '', $i['name'] ?? '', VipaymentCatalogSync::plainText($i['note'] ?? null), ...$this->prices($i), $i['status'] ?? '']);
        }
        rewind($fh);
        $path = 'vipayment/daftar-harga-vip.csv';
        $disk->put($path, stream_get_contents($fh));
        fclose($fh);

        return $path;
    }

    private function prices(array $item): array
    {
        $p = $item['price'] ?? [];

        return [(int) ($p['basic'] ?? 0), (int) ($p['premium'] ?? 0), (int) ($p['special'] ?? 0)];
    }

    private function rp(int $n): string
    {
        return number_format($n, 0, ',', '.');
    }
}
