<?php

use App\Models\LastFmPeriodTime;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {

        Schema::create('last_fm_period_times', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dateStart')
                  ->nullable();
            $table->dateTime('dateEnd')
                  ->nullable();
            $table->string('type')
                  ->default('overall');
            $table->timestamps();
        });

        Schema::table('last_fm_artist_stats', function (Blueprint $table) {
            $table->foreignIdFor(LastFmPeriodTime::class)
                  ->after('last_fm_user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {

        Schema::table('last_fm_artist_stats', function (Blueprint $table) {
            $table->dropForeignIdFor(LastFmPeriodTime::class);
            $table->dropColumn(['last_fm_period_time_id']);
        });

        Schema::dropIfExists('last_fm_period_times');

    }
};
