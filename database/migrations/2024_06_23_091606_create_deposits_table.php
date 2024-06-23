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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('send_to');
            $table->unsignedBigInteger('send_from');
            $table->string('payment_id');
            $table->string('method_type')->nullable();
            $table->string('image_path');
            $table->integer('amount');
            $table->enum('payment_type',['deposit','withdraw','transfer']);
            $table->enum('status',['pending','accepted','rejected'])->default('pending');
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
        Schema::dropIfExists('deposits');
    }
};
