<?php

/*
|--------------------------------------------------------------------------
| Skema Margin Keuntungan HustleStore
|--------------------------------------------------------------------------
|
| Harga jual dihitung dari HARGA MODAL (cost_price) dengan rumus:
|
|     harga_jual = max( modal * (1 + persen/100),  modal + min_add )
|     lalu dibulatkan KE ATAS ke kelipatan "rounding".
|
| Dengan begitu produk murah tetap untung (karena ada min_add / minimal
| keuntungan rupiah) dan produk mahal untung proporsional (persen).
|
| Silakan ubah angka di bawah ini kapan saja lalu jalankan tombol
| "Regenerasi Harga" di /admin/products untuk menerapkan ke semua produk.
|
*/

return [

    // Pembulatan harga jual ke atas (Rp). 100 = dibulatkan ke ratusan terdekat.
    'rounding' => 100,

    // persen = markup persentase, min_add = minimal keuntungan rupiah.
    'rules' => [
        'pulsa'     => ['member' => ['percent' => 3,  'min_add' => 1000], 'reseller' => ['percent' => 2,  'min_add' => 700],  'h2h' => ['percent' => 1.5, 'min_add' => 500]],
        'data'      => ['member' => ['percent' => 4,  'min_add' => 1500], 'reseller' => ['percent' => 3,  'min_add' => 1000], 'h2h' => ['percent' => 2,   'min_add' => 700]],
        'pln'       => ['member' => ['percent' => 0,  'min_add' => 2000], 'reseller' => ['percent' => 0,  'min_add' => 1500], 'h2h' => ['percent' => 0,   'min_add' => 1000]],
        'ewallet'   => ['member' => ['percent' => 3,  'min_add' => 1500], 'reseller' => ['percent' => 2,  'min_add' => 1000], 'h2h' => ['percent' => 1.5, 'min_add' => 700]],
        'game'      => ['member' => ['percent' => 8,  'min_add' => 1000], 'reseller' => ['percent' => 5,  'min_add' => 600],  'h2h' => ['percent' => 3,   'min_add' => 400]],
        'voucher'   => ['member' => ['percent' => 7,  'min_add' => 1000], 'reseller' => ['percent' => 4,  'min_add' => 600],  'h2h' => ['percent' => 2.5, 'min_add' => 400]],
        'streaming' => ['member' => ['percent' => 20, 'min_add' => 2000], 'reseller' => ['percent' => 12, 'min_add' => 1500], 'h2h' => ['percent' => 8,   'min_add' => 1000]],
        'sosmed'    => ['member' => ['percent' => 15, 'min_add' => 1000], 'reseller' => ['percent' => 10, 'min_add' => 700],  'h2h' => ['percent' => 6,   'min_add' => 500]],
    ],

    // Dipakai jika kategori brand tidak terdaftar di atas.
    'default' => ['member' => ['percent' => 8, 'min_add' => 1000], 'reseller' => ['percent' => 5, 'min_add' => 600], 'h2h' => ['percent' => 3, 'min_add' => 400]],
];
