<?php

namespace App\Services;

/**
 * Menghitung harga jual dari harga modal (cost) berdasarkan skema margin
 * yang didefinisikan di config/margin.php.
 */
class PriceCalculator
{
    /**
     * Hitung satu harga jual untuk tier tertentu (member|reseller|h2h).
     */
    public static function forTier(int $cost, string $category, string $tier): int
    {
        if ($cost <= 0) {
            return 0;
        }

        $rules = config('margin.rules', []);
        $rule = $rules[$category][$tier]
            ?? config("margin.default.{$tier}")
            ?? ['percent' => 8, 'min_add' => 1000];

        $byPercent = $cost * (1 + ((float) $rule['percent'] / 100));
        $byMinAdd  = $cost + (int) $rule['min_add'];
        $price     = max($byPercent, $byMinAdd);

        $rounding = (int) config('margin.rounding', 100);
        if ($rounding > 0) {
            $price = ceil($price / $rounding) * $rounding;
        }

        return (int) $price;
    }

    /**
     * Hitung semua tier sekaligus. Mengembalikan array siap disimpan ke Product.
     *
     * @return array{price:int, reseller_price:int, h2h_price:int}
     */
    public static function all(int $cost, string $category): array
    {
        return [
            'price'          => self::forTier($cost, $category, 'member'),
            'reseller_price' => self::forTier($cost, $category, 'reseller'),
            'h2h_price'      => self::forTier($cost, $category, 'h2h'),
        ];
    }
}
