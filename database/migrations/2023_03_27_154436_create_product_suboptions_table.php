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
        Schema::create('product_suboptions', function (Blueprint $table) {
            $table->id();
            $table->integer('op_id')->default('0');
            $table->string('sub_op_name')->nullable();
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
        Schema::dropIfExists('product_suboptions');
    }
};
