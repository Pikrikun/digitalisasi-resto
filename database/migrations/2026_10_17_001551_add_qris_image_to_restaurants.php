<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'qris_image')) {
                $table->string('qris_image')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('qris_image');
        });
    }
};
