<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->date('serve_date');
            $table->string('slot'); // breakfast | lunch | dinner
            $table->json('payload'); // aggregated meal quantities handed to logistics
            $table->unsignedInteger('total_meals');
            $table->string('status')->default('pending'); // pending | sent_to_logistics | delivered
            $table->timestamp('consolidated_at');
            $table->timestamps();

            // Consolidation runs once per outlet per slot per day
            $table->unique(['outlet_id', 'serve_date', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_orders');
    }
};
