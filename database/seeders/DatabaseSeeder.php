<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // CMS staff account — the only user allowed into /cms.
        User::updateOrCreate(
            ['email' => 'admin@ezfood.my'],
            ['name' => 'EzFood Admin', 'password' => 'password', 'is_admin' => true],
        );

        // Demo subscriber (worker/tenant) — no CMS access.
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        $this->call(OutletSeeder::class);
    }
}
