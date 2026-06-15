<?php

namespace Database\Seeders;

use App\Enums\DietaryPreference;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OutletSeeder::class);

        // CMS staff account — the only user allowed into /cms.
        User::updateOrCreate(
            ['email' => 'admin@ezfood.my'],
            ['name' => 'EzFood Admin', 'password' => 'password', 'is_admin' => true],
        );

        // Demo subscriber (worker/tenant) — no CMS access. Tenant-linked and
        // vegetarian so the registration/menu defaults are demonstrable locally.
        $demoOutlet = Outlet::first();
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'nickname' => 'Testy',
                'mobile_number' => '012-3456789',
                'company_name' => 'Demo Manufacturing Sdn Bhd',
                'dietary_preference' => DietaryPreference::Vegetarian,
                'password' => 'password',
                'outlet_id' => $demoOutlet?->id,
                'registered_qr_config_id' => $demoOutlet?->qrConfigs()->first()?->id,
            ],
        );
    }
}
