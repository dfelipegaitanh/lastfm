<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::table('last_fm_period_times', function (Blueprint $table) {
            $table->boolean('is_completed')
                  ->default(false)
                  ->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table('last_fm_period_times', function (Blueprint $table) {
            $table->dropColumn(['is_completed']);
        });
    }
};
