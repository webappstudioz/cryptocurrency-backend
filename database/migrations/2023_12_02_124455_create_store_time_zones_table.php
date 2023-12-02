<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_time_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_zone_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('winning_number');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_time_zones');
    }
};
