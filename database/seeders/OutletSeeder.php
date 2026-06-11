<?php

namespace Database\Seeders;

use App\Enums\CutoffBasis;
use App\Enums\MealMode;
use App\Enums\MealSlotType;
use App\Enums\OperationDays;
use App\Enums\OutletType;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        // A factory with multiple meals per day and per-slot cut-offs
        $factory = Outlet::create([
            'name' => 'Demo Factory Shah Alam',
            'type' => OutletType::Factory,
            'address' => 'Jalan Perusahaan 1, Seksyen 23, 40300 Shah Alam, Selangor',
            'contact_person' => 'Encik Rahman',
            'contact_phone' => '+60123456789',
        ]);

        $factoryConfig = $factory->qrConfigs()->create([
            'operation_days' => OperationDays::MonToSat,
            'is_24_hours' => true,
            'meal_mode' => MealMode::MultiplePerDay,
            'cutoff_basis' => CutoffBasis::PerSlot,
            'delivery_location' => 'Canteen Block B',
        ]);

        $factoryConfig->mealSlots()->createMany([
            ['slot' => MealSlotType::Breakfast, 'starts_at' => '07:00', 'ends_at' => '09:00', 'cutoff_time' => '05:30'],
            ['slot' => MealSlotType::Lunch, 'starts_at' => '12:00', 'ends_at' => '14:00', 'cutoff_time' => '10:00'],
            ['slot' => MealSlotType::Dinner, 'starts_at' => '19:00', 'ends_at' => '21:00', 'cutoff_time' => '17:00'],
        ]);

        $factory->meals()->createMany([
            ['name' => 'Nasi Lemak Ayam', 'price' => 8.50],
            ['name' => 'Mee Goreng Mamak', 'price' => 7.00],
            ['name' => 'Roti Canai Set', 'price' => 5.50, 'available_slots' => [MealSlotType::Breakfast->value]],
        ]);

        // A condo with one meal per day and a single daily cut-off
        $condo = Outlet::create([
            'name' => 'Demo Condo Mont Kiara',
            'type' => OutletType::Condo,
            'address' => 'Jalan Kiara 3, Mont Kiara, 50480 Kuala Lumpur',
            'contact_person' => 'Ms. Lim',
            'contact_phone' => '+60198765432',
        ]);

        $condoConfig = $condo->qrConfigs()->create([
            'operation_days' => OperationDays::MonToFri,
            'is_24_hours' => false,
            'opens_at' => '08:00',
            'closes_at' => '20:00',
            'meal_mode' => MealMode::SinglePerDay,
            'cutoff_basis' => CutoffBasis::PerDay,
            'daily_cutoff_time' => '10:00',
            'delivery_location' => 'Lobby concierge desk',
        ]);

        $condoConfig->mealSlots()->create([
            'slot' => MealSlotType::Lunch,
            'starts_at' => '12:00',
            'ends_at' => '14:00',
            'cutoff_time' => '10:00',
        ]);

        $condo->meals()->createMany([
            ['name' => 'Chicken Rice', 'price' => 9.00],
            ['name' => 'Vegetarian Bento', 'price' => 10.50],
        ]);
    }
}
