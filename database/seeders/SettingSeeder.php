<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Seed the HustleStore application identity settings.
     */
    public function run(): void
    {
        $defaults = [
            'site_name' => 'HustleStore',
            'site_logo' => 'logo_store.png',
            'site_font' => 'Plus Jakarta Sans',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Setting::clearCache();
    }
}
