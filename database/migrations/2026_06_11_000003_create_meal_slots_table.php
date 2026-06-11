<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_config_id')->constrained()->cascadeOnDelete();
            $table->string('slot'); // breakfast | lunch | dinner
            $table->time('starts_at');
            $table->time('ends_at');
            $table->time('cutoff_time'); // used when cutoff_basis = per_slot
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['qr_config_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_slots');
    }
};
