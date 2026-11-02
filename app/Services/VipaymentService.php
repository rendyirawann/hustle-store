<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Client API VIPayment (vip-reseller.co.id) — READ-ONLY.
 *
 * Sengaja hanya berisi endpoint baca (profile & daftar layanan). Tidak ada
 * method order: fulfillment pesanan HustleStore tetap dilakukan manual oleh admin.
 *
 * Catatan: VIPayment memakai whitelist IP (menu Profile → Pengaturan API).
 * Request dipaksa lewat IPv4 supaya IP yang terbaca konsisten dengan whitelist.
 */
class VipaymentService
{
    public function __construct(
        private readonly string $apiId,
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://vip-reseller.co.id/api',
    ) {}

    public static function fromConfig(): self
    {
        $cfg = config('services.vipayment');

        if (empty($cfg['api_id']) || empty($cfg['api_key'])) {
            throw new RuntimeException('VIPAYMENT_API_ID / VIPAYMENT_API_KEY belum diisi di .env');
        }

        return new self($cfg['api_id'], $cfg['api_key'], rtrim($cfg['base_url'], '/'));
    }

    /** md5(API ID + API KEY) sesuai dokumentasi VIPayment. */
    public function sign(): string
    {
        return md5($this->apiId . $this->apiKey);
    }

    /** Detail akun: full_name, username, balance, point, level. */
    public function profile(): array
    {
        return $this->post('profile');
    }

    /** Layanan Pulsa & PPOB (pulsa, paket data, PLN, e-money, dll). */
    public function prepaidServices(): array
    {
        return $this->post('prepaid', ['type' => 'services']);
    }

    /** Layanan Game & Streaming. */
    public function gameServices(): array
    {
        return $this->post('game-feature', ['type' => 'services']);
    }

    /** Layanan Sosial Media (perhatikan: type-nya "service", bukan "services"). */
    public function socialMediaServices(): array
    {
        return $this->post('social-media', ['type' => 'service']);
    }

    private function post(string $endpoint, array $params = []): array
    {
        $response = Http::asForm()
            ->acceptJson()
            ->timeout(90)
            ->withOptions(['force_ip_resolve' => 'v4'])
            ->retry(2, 1500, throw: false)
            ->post("{$this->baseUrl}/{$endpoint}", array_merge([
                'key'  => $this->apiKey,
                'sign' => $this->sign(),
            ], $params));

        $json = $response->json();

        if (! is_array($json)) {
            throw new RuntimeException("VIPayment [{$endpoint}] HTTP {$response->status()}: respons bukan JSON");
        }

        if (empty($json['result'])) {
            throw new RuntimeException("VIPayment [{$endpoint}]: " . ($json['message'] ?? 'gagal tanpa pesan'));
        }

        return is_array($json['data'] ?? null) ? $json['data'] : [];
    }
}
