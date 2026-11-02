<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Services\PriceCalculator;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Data awal katalog HustleStore (disalin dari price list provider).
     * cost = harga modal (kolom Member provider). Harga jual dihitung otomatis
     * dari config/margin.php. Item = [kode, nama, catatan, modal, status?].
     */
    public function run(): void
    {
        foreach ($this->brands() as $b) {
            $items = $b['items'];
            unset($b['items']);

            $brand = ProductBrand::updateOrCreate(['slug' => $b['slug']], $b);

            $sort = 0;
            foreach ($items as $it) {
                $code   = $it[0];
                $name   = $it[1];
                $note   = $it[2] ?? null;
                $cost   = (int) $it[3];
                $status = $it[4] ?? 'available';

                $prices = PriceCalculator::all($cost, $brand->category);

                Product::updateOrCreate(
                    ['code' => $code],
                    array_merge([
                        'brand_id'   => $brand->id,
                        'name'       => $name,
                        'note'       => $note,
                        'cost_price' => $cost,
                        'status'     => $status,
                        'is_active'  => true,
                        'sort_order' => $sort++,
                    ], $prices)
                );
            }
        }
    }

    private function brands(): array
    {
        $R = 'Reguler';
        $PLN = 'Masukkan nomor meter / ID pelanggan';

        return [
            // ================= PULSA & PPOB =================
            [
                'name' => 'AXIS', 'slug' => 'axis', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 1,
                'items' => [
                    ['AX5', 'Axis 5.000', $R, 6058], ['AX10', 'Axis 10.000', $R, 11058],
                    ['AX15', 'Axis 15.000', $R, 15202], ['AX25', 'Axis 25.000', $R, 25710],
                    ['AX30', 'Axis 30.000', $R, 30305], ['AX50', 'Axis 50.000', $R, 50195],
                    ['AX100', 'Axis 100.000', $R, 99975], ['AX200', 'Axis 200.000', $R, 200425],
                ],
            ],
            [
                'name' => 'BY.U', 'slug' => 'byu', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 2,
                'items' => [
                    ['BYU1', 'By.U 1.000', $R, 2105, 'empty'], ['BYU2', 'By.U 2.000', $R, 2925],
                    ['BYU3', 'By.U 3.000', $R, 3930], ['BYU4', 'By.U 4.000', $R, 4930],
                    ['BYU5', 'By.U 5.000', $R, 5395], ['BYU6', 'By.U 6.000', $R, 6930],
                    ['BYU7', 'By.U 7.000', $R, 8055], ['BYU8', 'By.U 8.000', $R, 8930],
                    ['BYU9', 'By.U 9.000', $R, 10055], ['BYU10', 'By.U 10.000', $R, 10555],
                    ['BYU15', 'By.U 15.000', $R, 15210], ['BYU20', 'By.U 20.000', $R, 20162],
                    ['BYU25', 'By.U 25.000', $R, 24925], ['BYU30', 'By.U 30.000', $R, 29750],
                    ['BYU35', 'By.U 35.000', $R, 34689], ['BYU40', 'By.U 40.000', $R, 39927],
                    ['BYU45', 'By.U 45.000', $R, 44920], ['BYU50', 'By.U 50.000', $R, 49415],
                    ['BYU55', 'By.U 55.000', $R, 55225], ['BYU60', 'By.U 60.000', $R, 59373, 'empty'],
                    ['BYU65', 'By.U 65.000', $R, 64227], ['BYU70', 'By.U 70.000', $R, 69175],
                    ['BYU75', 'By.U 75.000', $R, 73290], ['BYU80', 'By.U 80.000', $R, 79045],
                    ['BYU85', 'By.U 85.000', $R, 83917], ['BYU90', 'By.U 90.000', $R, 88840],
                    ['BYU95', 'By.U 95.000', $R, 93810], ['BYU100', 'By.U 100.000', $R, 97765],
                    ['BYU150', 'By.U 150.000', $R, 148925], ['BYU200', 'By.U 200.000', $R, 194015],
                ],
            ],
            [
                'name' => 'INDOSAT', 'slug' => 'indosat', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 3,
                'items' => [
                    ['IM2', 'Indosat 2.000', $R, 2545, 'empty'], ['IP5', 'Indosat 5.000', $R, 6796],
                    ['IM5', 'Indosat 5.000', $R, 7235], ['IM10', 'Indosat 10.000', $R, 10885, 'empty'],
                    ['I10', 'Indosat 10.000', $R, 11630], ['I102', 'Indosat 10.000', $R, 12255],
                    ['I12', 'Indosat 12.000', $R, 13910], ['I15', 'Indosat 15.000', $R, 16150],
                    ['I20', 'Indosat 20.000', $R, 21092], ['I25', 'Indosat 25.000', $R, 26050],
                    ['I30', 'Indosat 30.000', $R, 30890], ['I40', 'Indosat 40.000', $R, 39927],
                    ['I50', 'Indosat 50.000', $R, 49980], ['I60', 'Indosat 60.000', $R, 59830],
                    ['I80', 'Indosat 80.000', $R, 77825], ['I90', 'Indosat 90.000', $R, 89061],
                    ['I100', 'Indosat 100.000', $R, 100975], ['I125', 'Indosat 125.000', $R, 124122],
                    ['I150', 'Indosat 150.000', $R, 147240], ['I175', 'Indosat 175.000', $R, 173325],
                    ['I200', 'Indosat 200.000', $R, 198145], ['I250', 'Indosat 250.000', $R, 250475, 'empty'],
                    ['I500', 'Indosat 500.000', $R, 498225, 'empty'], ['I1000', 'Indosat 1.000.000', $R, 925625, 'empty'],
                ],
            ],
            [
                'name' => 'SMARTFREN', 'slug' => 'smartfren', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 4,
                'items' => [
                    ['SMT5', 'Smart 5.000', $R, 5209], ['SMT10', 'Smart 10.000', $R, 10210],
                    ['SMT15', 'Smart 15.000', $R, 15270], ['SMT20', 'Smart 20.000', $R, 20215],
                    ['SMT25', 'Smart 25.000', $R, 25213], ['SMT30', 'Smart 30.000', $R, 30225],
                    ['SMT50', 'Smart 50.000', $R, 50215], ['SMT60', 'Smart 60.000', $R, 60135],
                    ['SMT75', 'Smart 75.000', $R, 75045], ['SMT100', 'Smart 100.000', $R, 100140],
                    ['SMT150', 'Smart 150.000', $R, 150325],
                ],
            ],
            [
                'name' => 'TELKOMSEL', 'slug' => 'telkomsel', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 5,
                'items' => [
                    ['SM2', 'Telkomsel 2.000', $R, 3115], ['SM3', 'Telkomsel 3.000', $R, 3930],
                    ['SM4', 'Telkomsel 4.000', $R, 4975], ['SP5', 'Telkomsel 5.000', $R, 5380],
                    ['SM5', 'Telkomsel 5.000', $R, 5600], ['SP10', 'Telkomsel 10.000', $R, 10400],
                    ['SM10', 'Telkomsel 10.000', $R, 10535], ['SM15', 'Telkomsel 15.000', $R, 14965],
                    ['SP15', 'Telkomsel 15.000', $R, 14989], ['SM20', 'Telkomsel 20.000', $R, 19950],
                    ['S20', 'Telkomsel 20.000', $R, 20240], ['SM25', 'Telkomsel 25.000', $R, 24819],
                    ['SM30', 'Telkomsel 30.000', $R, 29750], ['SM35', 'Telkomsel 35.000', $R, 34730],
                    ['SM40', 'Telkomsel 40.000', $R, 39400], ['SM45', 'Telkomsel 45.000', $R, 44790],
                    ['SM50', 'Telkomsel 50.000', $R, 50100], ['SM60', 'Telkomsel 60.000', $R, 59356],
                    ['SM65', 'Telkomsel 65.000', $R, 64315], ['SM70', 'Telkomsel 70.000', $R, 69210],
                    ['SP75', 'Telkomsel 75.000', $R, 73050], ['SM75', 'Telkomsel 75.000', $R, 73100],
                    ['SM80', 'Telkomsel 80.000', $R, 79045], ['SM85', 'Telkomsel 85.000', $R, 83985],
                    ['SM90', 'Telkomsel 90.000', $R, 88933], ['SM95', 'Telkomsel 95.000', $R, 93869],
                    ['SP100', 'Telkomsel 100.000', $R, 97425], ['SM100', 'Telkomsel 100.000', $R, 99050],
                    ['SM150', 'Telkomsel 150.000', $R, 148925], ['SM200', 'Telkomsel 200.000', $R, 195450],
                    ['SM300', 'Telkomsel 300.000', $R, 297590],
                ],
            ],
            [
                'name' => 'TRI', 'slug' => 'tri', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 6,
                'items' => [
                    ['T1', 'Three 1.000', $R, 1530, 'empty'], ['T2', 'Three 2.000', $R, 2510, 'empty'],
                    ['T3', 'Three 3.000', $R, 3461, 'cutoff'], ['T4', 'Three 4.000', $R, 4643, 'empty'],
                    ['T5', 'Three 5.000', $R, 6805], ['T10', 'Three 10.000', $R, 11805],
                    ['T15', 'Three 15.000', $R, 16510], ['T20', 'Three 20.000', $R, 20700],
                    ['T25', 'Three 25.000', $R, 26460], ['T30', 'Three 30.000', $R, 29800],
                    ['T40', 'Three 40.000', $R, 39605], ['T50', 'Three 50.000', $R, 50345],
                    ['T60', 'Three 60.000', $R, 59692], ['T75', 'Three 75.000', $R, 74785],
                    ['T80', 'Three 80.000', $R, 78475], ['T100', 'Three 100.000', $R, 100325],
                    ['T150', 'Three 150.000', $R, 144870], ['T200', 'Three 200.000', $R, 198215],
                    ['T300', 'Three 300.000', $R, 290750, 'empty'], ['T500', 'Three 500.000', $R, 485100, 'empty'],
                    ['T1000', 'Three 1.000.000', $R, 980200, 'empty'],
                ],
            ],
            [
                'name' => 'XL', 'slug' => 'xl', 'tab' => 'pulsa_ppob', 'category' => 'pulsa',
                'input_label' => 'Nomor HP', 'sort_order' => 7,
                'items' => [
                    ['XLP5', 'Xl 5.000', $R, 6405], ['XLP10', 'Xl 10.000', $R, 11058],
                    ['XLP15', 'Xl 15.000', $R, 15255], ['XLP20', 'Xl 20.000', $R, 22040, 'empty'],
                    ['XLP25', 'Xl 25.000', $R, 25199], ['XLP30', 'XL 30.000', $R, 30255],
                    ['XLP40', 'XL 40.000', $R, 40235], ['XLP50', 'Xl 50.000', $R, 51025],
                    ['XLP60', 'Xl 60.000', $R, 60275], ['XLP70', 'Xl 70.000', $R, 70250],
                    ['XLP80', 'Xl 80.000', $R, 80255], ['XLP90', 'Xl 90.000', $R, 90250],
                    ['XLP100', 'Xl 100.000', $R, 99975], ['XLP150', 'Xl 150.000', $R, 149995],
                    ['XLP200', 'Xl 200.000', $R, 200325], ['XLP300', 'Xl 300.000', 'Pulsa Xl Rp 300.000', 295750, 'empty'],
                    ['XLP500', 'Xl 500.000', 'Pulsa Xl Rp 500.000', 492325, 'empty'],
                    ['XLP1000', 'Xl 1.000.000', 'Pulsa Xl Rp 1.000.000', 993825, 'empty'],
                ],
            ],
            [
                'name' => 'PLN Token', 'slug' => 'pln', 'tab' => 'pulsa_ppob', 'category' => 'pln',
                'input_label' => 'Nomor Meter / ID Pelanggan', 'sort_order' => 8,
                'items' => [
                    ['PLN5', 'PLN 5.000', $PLN, 6715], ['PLN10', 'PLN 10.000', $PLN, 11715, 'empty'],
                    ['PLN15', 'PLN 15.000', $PLN, 17005], ['PLN20', 'PLN 20.000', $PLN, 21880],
                    ['PLN50', 'PLN 50.000', $PLN, 52185], ['PLN100', 'PLN 100.000', $PLN, 101850],
                    ['PLN200', 'PLN 200.000', $PLN, 202019], ['PLN500', 'PLN 500.000', $PLN, 501865],
                ],
            ],

            // ================= GAME & STREAMING =================
            [
                'name' => 'Call of Duty Mobile (Indonesia)', 'slug' => 'cod-mobile', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID', 'sort_order' => 20,
                'items' => [
                    ['CODM63-S13', '63 CP', null, 9347], ['CODM128-S13', '128 CP', null, 18693],
                    ['CODM321-S13', '321 CP', null, 46732], ['CODM645-S13', '645 CP', null, 93438],
                    ['CODM800-S13', '800 CP', null, 109643], ['CODM1373-S13', '1373 CP', null, 186849],
                    ['CODM2060-S13', '2060 CP', null, 281374], ['CODM2750-S13', '2750 CP', null, 368507],
                    ['CODM3564-S13', '3564 CP', null, 467085], ['CODM7656-S13', '7656 CP', null, 935169],
                    ['CODM15312-S13', '15312 CP', null, 2025190], ['CODM38280-S13', '38280 CP', null, 4799639],
                ],
            ],
            [
                'name' => 'EA SPORTS FC Mobile', 'slug' => 'ea-fc-mobile', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'Login', 'sort_order' => 21,
                'items' => [
                    ['FCMBL40-S10', '40 FC POINTS', null, 6325], ['FCMBLS39-S10', '39 Silver', null, 6325],
                    ['FCMBL100-S10', '100 FC POINTS', null, 15570], ['FCMBLS99-S10', '99 Silver', null, 15570],
                    ['FCMBL520-S10', '520 FC POINTS', null, 76878], ['FCMBLS499-S10', '499 Silver', null, 76878],
                    ['FCMBL1070-S10', '1070 FC POINTS', null, 154729], ['FCMBLS999-S10', '999 Silver', null, 154729],
                    ['FCMBL2200-S10', '2200 FC POINTS', null, 320162], ['FCMBLS1999-S10', '1999 Silver', null, 320162],
                    ['FCMBL5750-S10', '5750 FC POINTS', null, 777535], ['FCMBLS4999-S10', '4999 Silver', null, 777535],
                    ['FCMBL12000-S10', '12000 FC POINTS', null, 1556043], ['FCMBLS9999-S10', '9999 Silver', null, 1556043],
                ],
            ],
            [
                // Free Fire: harga adalah PERKIRAAN (list asli sangat panjang & sulit dibaca).
                // Silakan sesuaikan modal di /admin/products lalu klik "Regenerasi Harga".
                'name' => 'Free Fire', 'slug' => 'free-fire', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID', 'sort_order' => 22,
                'items' => [
                    ['FF5-S13', '5 Diamonds', 'Perkiraan harga', 850], ['FF12-S13', '12 Diamonds', 'Perkiraan harga', 1900],
                    ['FF50-S13', '50 Diamonds', 'Perkiraan harga', 7400], ['FF70-S13', '70 Diamonds', 'Perkiraan harga', 10200],
                    ['FF100-S13', '100 Diamonds', 'Perkiraan harga', 14400], ['FF140-S13', '140 Diamonds', 'Perkiraan harga', 20000],
                    ['FF210-S13', '210 Diamonds', 'Perkiraan harga', 30000], ['FF355-S13', '355 Diamonds', 'Perkiraan harga', 50000],
                    ['FF720-S13', '720 Diamonds', 'Perkiraan harga', 99000], ['FF1450-S13', '1450 Diamonds', 'Perkiraan harga', 198000],
                    ['FF2180-S13', '2180 Diamonds', 'Perkiraan harga', 297000], ['FFWEEKLY-S13', 'Membership Mingguan', 'Perkiraan harga', 29000],
                    ['FFMONTHLY-S13', 'Membership Bulanan', 'Perkiraan harga', 88000],
                ],
            ],
            [
                'name' => 'Honkai Star Rail', 'slug' => 'honkai-star-rail', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'UID (Server)', 'sort_order' => 23,
                'items' => [
                    ['HSTR60-S13', '60 Oneiric Shard', null, 17304], ['HSTR300-S13', '300 + 30 Oneiric Shard', null, 85408],
                    ['HSTRESP-S13', 'Express Supply Pass', null, 85408], ['HSTR980-S13', '980 + 110 Oneiric Shard', null, 269088],
                    ['HSTR1980-S13', '1980 + 260 Oneiric Shard', null, 517596], ['HSTR3280-S13', '3280 + 600 Oneiric Shard', null, 863346],
                    ['HSTR6480-S13', '6480 + 1600 Oneiric Shard', null, 1575252],
                ],
            ],
            [
                'name' => 'Honor of Kings Global', 'slug' => 'honor-of-kings', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID (Server)', 'sort_order' => 24,
                'items' => [
                    ['HOK80-S21', '80 Tokens', null, 14700], ['HOK80-S13', '80 Tokens', null, 15094],
                    ['HOKWC-S13', 'Weekly Card', null, 16759], ['HOK240-S21', '240 Tokens', null, 43350],
                    ['HOK240-S13', '240 Tokens', null, 45096], ['HOKWCP-S13', 'Weekly Card Plus', null, 51472],
                    ['HOK400-S21', '400 Tokens', null, 73800], ['HOK400-S13', '400 Tokens', null, 75217],
                    ['HOK560-S21', '560 Tokens', null, 100650], ['HOK560-S13', '560 Tokens', null, 105381],
                    ['HOK800-S21', '800 Tokens', null, 147600], ['HOK800-S13', '800 Tokens', null, 150451],
                    ['HOK1200-S21', '1200 Tokens', null, 216900], ['HOK1200-S13', '1.200 Tokens', null, 229008],
                    ['HOK2400-S21', '2400 Tokens', null, 442950], ['HOK2400-S13', '2.400 Tokens', null, 451432],
                    ['HOK4000-S21', '4000 Tokens', null, 738150], ['HOK4000-S13', '4.000 Tokens', null, 752456],
                    ['HOK8000-S21', '8000 Tokens', null, 1475400], ['HOK8000-S13', '8.000 Tokens', null, 1504883],
                ],
            ],
            [
                'name' => 'Mobile Legends (Global)', 'slug' => 'mobile-legends-global', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID (Zone ID)', 'sort_order' => 25,
                'items' => [
                    ['MLGLOBAL22590-S14', '55 Diamonds (50 + 5 Bonus) 2x Bonus First Top Up', null, 13600],
                    ['MLGLOBAL13-S14', '86 Diamonds (78 + 8 Bonus)', null, 21446],
                    ['MLGLOBAL16642-S14', '1x Weekly Diamond Pass (Event Topup 100)', null, 26502],
                    ['MLGLOBAL22591-S14', '165 Diamonds (150 + 15 Bonus) 2x Bonus First Top Up', null, 40765],
                    ['MLGLOBAL23-S14', '172 Diamonds (156 + 16 Bonus)', null, 42543],
                    ['MLGLOBAL25-S14', '257 Diamonds (234 + 23 Bonus)', null, 61618],
                    ['MLGLOBAL22592-S14', '275 Diamonds (250 + 25 Bonus) 2x Bonus First Top Up', null, 65384],
                    ['MLGLOBAL22593-S14', '565 Diamonds (500 + 65 Bonus) 2x Bonus First Top Up', null, 134255],
                    ['MLGLOBAL33-S14', 'Twilight Pass', null, 140357],
                    ['MLGLOBAL26-S14', '706 Diamonds (625 + 81 Bonus)', null, 167382],
                    ['MLGLOBAL27-S14', '2195 Diamonds (1860 + 335 Bonus)', null, 506679],
                    ['MLGLOBAL28-S14', '3688 Diamonds (3099 + 589 Bonus)', null, 845279],
                    ['MLGLOBAL29-S14', '5532 Diamonds (4649 + 883 Bonus)', null, 1276286],
                    ['MLGLOBAL30-S14', '9288 Diamonds (7740 + 1548 Bonus)', null, 2119821],
                ],
            ],
            [
                'name' => 'PUBG Mobile (Global)', 'slug' => 'pubg-mobile-global', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID', 'sort_order' => 26,
                'items' => [
                    ['PUBGMGLOBAL60-S11', '60 UC (60 + 0)', null, 15600], ['PUBGMGLOBAL60UC-S13', '60 UC (60 + 0) Instant', null, 16395],
                    ['PUBGMGLOBAL325-S11', '325 UC (300 + 25)', null, 78000], ['PUBGMGLOBAL325UC-S13', '325 UC (300 + 25) Instant', null, 82694],
                    ['PUBGMGLOBAL660-S11', '660 UC (600 + 60)', null, 156000], ['PUBGMGLOBAL660UC-S13', '660 UC (600 + 60) Instant', null, 165548],
                    ['PUBGMGLOBAL1800-S11', '1800 UC (1500 + 300)', null, 390000], ['PUBGMGLOBAL1800UC-S13', '1800 UC (1500 + 300) Instant', null, 414311],
                    ['PUBGMGLOBAL3850-S11', '3850 UC (3000 + 850)', null, 780000], ['PUBGMGLOBAL3850UC-S13', '3850 UC (3000 + 850) Instant', null, 828597],
                    ['PUBGMGLOBAL8100-S11', '8100 UC (6000 + 2100)', null, 1560000], ['PUBGMGLOBAL8100UC-S13', '8100 UC (6000 + 2100) Instant', null, 1656920],
                ],
            ],
            [
                'name' => 'PUBG Mobile (ID)', 'slug' => 'pubg-mobile-id', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'User ID', 'sort_order' => 27,
                'items' => [
                    ['PUBGMINDO60-S13', '60 UC', null, 16492], ['PUBGMINDO60-S14', '60 UC (60 + 0)', null, 16848],
                    ['PUBGMINDO120-S14', '120 UC (120 + 0)', null, 33696], ['PUBGMINDO180-S14', '180 UC (180 + 0)', null, 50544],
                    ['PUBGMINDO325-S13', '325 UC', null, 80842], ['PUBGMINDO325-S14', '325 UC (300 + 25)', null, 84240],
                    ['PUBGMINDO385-S14', '385 UC (360 + 25)', null, 101088], ['PUBGMINDO445-S14', '445 UC (420 + 25)', null, 117936],
                    ['PUBGMINDO505-S14', '505 UC (480 + 25)', null, 134784], ['PUBGMINDO660-S13', '660 UC', null, 161682],
                    ['PUBGMINDO660-S14', '660 UC (600 + 60)', null, 168480], ['PUBGMINDO650-S14', '650 UC (600 + 50)', null, 168480],
                    ['PUBGMINDO720-S14', '720 UC (600 + 120)', null, 185328], ['PUBGMINDO975-S14', '975 UC (900 + 75)', null, 252720],
                    ['PUBGMINDO985-S14', '985 UC (900 + 85)', null, 252720], ['PUBGMINDO1045-S14', '1045 UC (950 + 85)', null, 269568],
                    ['PUBGMINDO1320-S14', '1320 UC (1200 + 120)', null, 336960], ['PUBGMINDO1800-S13', '1800 UC', null, 404208],
                    ['PUBGMINDO1800-S14', '1800 UC (1500 + 300)', null, 421200], ['PUBGMINDO1860-S14', '1860 UC (1560 + 300)', null, 438048],
                    ['PUBGMINDO2125-S14', '2125 UC (1800 + 325)', null, 505440], ['PUBGMINDO2460-S14', '2460 UC (2100 + 360)', null, 589680],
                    ['PUBGMINDO2785-S14', '2785 UC (2400 + 385)', null, 673920], ['PUBGMINDO3850-S13', '3850 UC', null, 808418],
                    ['PUBGMINDO3850-S14', '3850 UC (3000 + 850)', null, 842400], ['PUBGMINDO3925-S14', '3925 UC (3300 + 625)', null, 926640],
                    ['PUBGMINDO4500-S14', '4500 UC (3600 + 900)', null, 1010880], ['PUBGMINDO5650-S14', '5650 UC (4500 + 1150)', null, 1263600],
                    ['PUBGMINDO6310-S14', '6310 UC (5100 + 1210)', null, 1432080], ['PUBGMINDO8100-S13', '8100 UC', null, 1659135],
                    ['PUBGMINDO8100-S14', '8100 UC (6000 + 2100)', null, 1684800],
                ],
            ],
            [
                'name' => 'Roblox Via Login', 'slug' => 'roblox-via-login', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'Username & Password', 'sort_order' => 28,
                'items' => [
                    ['ROBVILOG80-S17', '80 Robux', null, 14800], ['ROBVILOG160-S23', '160 Robux', null, 29600],
                    ['ROBVILOG240-S23', '240 Robux', null, 44400], ['ROBVILOG320-S23', '320 Robux', null, 57600],
                    ['ROBVILOG500-S17', '500 Robux', null, 74000], ['ROBVILOG1000-S17', '1000 Robux', null, 148000],
                    ['ROBVILOG1500-S17', '1500 Robux', null, 222000], ['ROBVILOG2000-S17', '2000 Robux', null, 296000],
                    ['ROBVILOG2500-S17', '2500 Robux', null, 370000], ['ROBVILOG3000-S17', '3000 Robux', null, 444000],
                    ['ROBVILOG3500-S17', '3500 Robux', null, 518000], ['ROBVILOG4000-S17', '4000 Robux', null, 592000],
                    ['ROBVILOG4500-S17', '4500 Robux', null, 666000], ['ROBVILOG5000-S17', '5000 Robux', null, 740000],
                    ['ROBVILOG5500-S17', '5500 Robux', null, 814000], ['ROBVILOG10000-S17', '10000 Robux', null, 1480000],
                ],
            ],
            [
                'name' => 'Valorant', 'slug' => 'valorant', 'tab' => 'game_streaming', 'category' => 'game',
                'input_label' => 'Riot ID', 'sort_order' => 29,
                'items' => [
                    ['VAL475-S10', '475 Points', null, 53336], ['VAL1000-S10', '1000 Points', null, 106672],
                    ['VAL1475-S10', '1475 Points', null, 160009], ['VAL2050-S10', '2050 Points', null, 213345],
                    ['VAL2525-S10', '2525 Points', null, 266680], ['VAL3050-S10', '3050 Points', null, 320016],
                    ['VAL3650-S10', '3650 Points', null, 370495], ['VAL4125-S10', '4125 Points', null, 423831],
                    ['VAL4650-S10', '4650 Points', null, 477167], ['VAL5350-S10', '5350 Points', null, 532409],
                    ['VAL5700-S10', '5700 Points', null, 583840], ['VAL5825-S10', '5825 Points', null, 585744],
                    ['VAL6350-S10', '6350 Points', null, 639080], ['VAL7400-S10', '7400 Points', null, 745753],
                    ['VAL9000-S10', '9000 Points', null, 902904], ['VAL11000-S10', '11000 Points', null, 1046721],
                    ['VAL11475-S10', '11475 Points', null, 1100057], ['VAL12000-S10', '12000 Points', null, 1153393],
                    ['VAL13050-S10', '13050 Points', null, 1260065], ['VAL14650-S10', '14650 Points', null, 1417216],
                    ['VAL16350-S10', '16350 Points', null, 1579129], ['VAL22000-S10', '22000 Points', null, 2093442],
                ],
            ],
            [
                'name' => 'Voucher Roblox', 'slug' => 'voucher-roblox', 'tab' => 'game_streaming', 'category' => 'voucher',
                'input_label' => 'Email (untuk kirim kode)', 'sort_order' => 30,
                'items' => [
                    ['ROB10USD-S17', 'USD $10 Global', null, 163020], ['ROB25USD-S17', 'USD $25 Global', null, 407628],
                    ['ROB50USD-S17', 'USD $50 Global', null, 815100], ['ROB10USDx5-S17', 'USD $10 X5', null, 815100],
                    ['ROB25USDx5-S17', 'USD $25 X5', null, 2038140], ['ROB50USDx5-S17', 'USD $50 X5', null, 4075500],
                ],
            ],
            [
                'name' => 'Amazon Prime Video', 'slug' => 'amazon-prime-video', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email / No. WhatsApp', 'sort_order' => 31,
                'items' => [
                    ['AMZPRIME1P1U-S1', 'Shared 1 Bulan [ 1 PROFILE - 1 USER ]', null, 3500],
                    ['AMZPRIME5P-S1', 'Private 1 Bulan [ 1 AKUN - 5 PROFILE ]', null, 11000],
                ],
            ],
            [
                'name' => 'Canva Pro', 'slug' => 'canva-pro', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email', 'sort_order' => 32,
                'items' => [
                    ['CANVAANGGOTA1BLN-S1', 'Canva Pro Anggota [ 1 Bulan ] [ Garansi 1 Bulan ]', null, 1300],
                    ['CANVAANGGOTA2BLN-S1', 'Canva Pro Anggota [ 2 Bulan ] [ Garansi 2 Bulan ]', null, 2500],
                    ['CANVADESAINER1BLN-S1', 'Canva Pro Desainer [ 1 Bulan ] [ Garansi 1 Bulan ]', null, 2300],
                    ['CANVAEDU1THN-S1', 'Canva Education [ 1 Tahun ] [ Garansi 6 Bulan ]', null, 11500],
                ],
            ],
            [
                'name' => 'CapCut Pro', 'slug' => 'capcut-pro', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email', 'sort_order' => 33,
                'items' => [
                    ['CAPCUTPRIVATE7D-S1', 'Private Akun 7 Hari [ Garansi 3 Hari ]', null, 10500],
                ],
            ],
            [
                'name' => 'iQIYI Premium', 'slug' => 'iqiyi-premium', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email / No. WhatsApp', 'sort_order' => 34,
                'items' => [
                    ['IQIYISHARED-S1', 'iQIYI Premium [ 1 Bulan ] [ Shared ]', null, 10000],
                ],
            ],
            [
                'name' => 'Vidio Premier', 'slug' => 'vidio-premier', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email / No. WhatsApp', 'sort_order' => 35,
                'items' => [
                    ['VIDIOTV1THNGAR1B-S1', 'Video Premier Platinum [ 1 Tahun ] [ KHUSUS TV ] [ Garansi 1 Bulan ]', null, 8000],
                    ['VIDIOTV1THNGAR3B-S1', 'Video Premier Platinum [ 1 Tahun ] [ KHUSUS TV ] [ Garansi 3 Bulan ]', null, 15000],
                    ['VIDIOSHAREDMBL1BLN-S2', 'Video Premier Platinum [ 1 Bulan ] [ Shared 2 User - 1 Devices - Khusus Mobile ]', null, 14750],
                    ['VIDIOPLATMOBPRIVATE-S1', 'Video Premier Platinum [ 1 Bulan ] [ Private - Khusus Mobile ]', null, 27000],
                ],
            ],
            [
                'name' => 'Viu Premium', 'slug' => 'viu-premium', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email / No. WhatsApp', 'sort_order' => 36,
                'items' => [
                    ['VIU1THNGAR1BLN-S1', 'Private 12 Bulan [ AKUN - 1 DEVICES ] [ GARANSI 1 BULAN ]', null, 4500],
                    ['VIU1THNGAR3BLN-S1', 'Private 12 Bulan [ AKUN - 1 DEVICES ] [ GARANSI 3 BULAN ]', null, 6500],
                ],
            ],
            [
                'name' => 'WeTV Premium', 'slug' => 'wetv-premium', 'tab' => 'game_streaming', 'category' => 'streaming',
                'input_label' => 'Email / No. WhatsApp', 'sort_order' => 37,
                'items' => [
                    ['WETVSHARED1BLN-S2', 'WETV Premium [ 1 Bulan ] [ Shared - 1 Devices ]', null, 8500],
                    ['WETVPRIVATE1BLN-S1', 'WETV Premium [ 1 Bulan ] [ Private ]', null, 30000],
                ],
            ],
        ];
    }
}
