<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('payment_providers_enabled', ['fedapay']);
        Setting::set('payment_provider_default', 'fedapay');
        Setting::set('kkiapay_environment', 'sandbox');
    }
}
