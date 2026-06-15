<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('name');
            $table->string('mobile_number', 20)->nullable()->after('nickname');
            $table->string('company_name')->nullable()->after('mobile_number');
            $table->string('dietary_preference')->nullable()->after('company_name'); // vegetarian | non_vegetarian
            // Nullable: admins and pre-AF-7 users have no tenant; required-ness is
            // enforced at the registration boundary, not the schema.
            $table->foreignId('outlet_id')->nullable()->after('is_admin')->constrained()->nullOnDelete();
            $table->foreignId('registered_qr_config_id')->nullable()->after('outlet_id')
                ->constrained('qr_configs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registered_qr_config_id');
            $table->dropConstrainedForeignId('outlet_id');
            $table->dropColumn(['nickname', 'mobile_number', 'company_name', 'dietary_preference']);
        });
    }
};
