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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('reject_id')->after('status')->nullable();
            $table->longText('description')->after('reject_id')->nullable();
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->float('wallet_amount')->after('zip_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reject_id','description']);
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['wallet_amount']);
        });
    }
};
