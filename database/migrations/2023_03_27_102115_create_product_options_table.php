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
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->default('0');
            $table->integer('img_id')->default('0');
            $table->string('op_name')->nullable();
            $table->string('img_name')->nullable();
            $table->double('price', 15, 2)->default('0.0');
            $table->integer('stock')->default('0');
            $table->string('sku')->nullable();
            $table->integer('status')->default('0');
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
        Schema::dropIfExists('product_options');
    }
};
