<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_id')->constrained()->restrictOnDelete();
            $table->date('serve_date');
            $table->string('slot'); // breakfast | lunch | dinner
            $table->string('status')->default('selected'); // selected | locked | cancelled
            $table->timestamps();

            // One selection per subscriber per slot per day
            $table->unique(['subscription_id', 'serve_date', 'slot']);
            $table->index(['serve_date', 'slot', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_selections');
    }
};
