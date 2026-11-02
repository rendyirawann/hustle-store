<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Sinkron harga modal & status stok produk dari VIPayment tiap 30 menit.
| Harga jual ikut dihitung ulang dari config/margin.php bila modal berubah.
| Di server WAJIB ada cron:  * * * * * php /path/ke/app/artisan schedule:run
| (IP server juga harus masuk Whitelist IP di VIPayment → Pengaturan API.)
*/
Schedule::command('vip:sync')
    ->everyThirtyMinutes()
    ->withoutOverlapping(15)
    ->appendOutputTo(storage_path('logs/vip-sync.log'));
