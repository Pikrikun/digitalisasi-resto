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
            $table->time('open_time')->default('08:00:00')->after('phone');
            $table->time('close_time')->default('22:00:00')->after('open_time');
            $table->string('status')->default('open')->after('close_time'); // open, closed
            $table->string('security_pin')->nullable()->after('status'); // 6 digit PIN
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time', 'status', 'security_pin']);
        });
    }
};
