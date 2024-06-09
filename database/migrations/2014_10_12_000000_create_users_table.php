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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->string('parent_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('supponser_by')->nullable();
            $table->string('referrel_Code');
            $table->string('phone_number')->nullable();
            $table->string('country_id')->nullable();
            $table->string('security_key')->nullable();
            $table->tinyInteger('status')->default(1); 
            $table->tinyInteger('verified')->default(0);
            $table->date('joining_date')->nullable();
            $table->tinyInteger('term_condition')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
