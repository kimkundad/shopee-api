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
        Schema::create('ownershop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('user_code');
            $table->string('setting');
            $table->int('setting_bill')->defaultValue(1);
            $table->int('setting_invoice')->defaultValue(1);
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
        Schema::dropIfExists('ownershop_settings');
    }
};
