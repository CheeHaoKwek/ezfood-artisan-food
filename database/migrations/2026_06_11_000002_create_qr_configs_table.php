<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique(); // identifier encoded in the printed QR
            $table->string('operation_days'); // mon_fri | mon_sat | mon_sun
            $table->boolean('is_24_hours')->default(false);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->string('meal_mode'); // single | multiple
            $table->string('cutoff_basis')->default('per_slot'); // per_slot | per_day
            $table->time('daily_cutoff_time')->nullable(); // used when cutoff_basis = per_day
            $table->string('delivery_location');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_configs');
    }
};
