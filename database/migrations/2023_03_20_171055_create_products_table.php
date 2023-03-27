<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. sku
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_product')->nullable();
            $table->text('detail_product')->nullable();
            $table->string('img_product')->nullable();
            $table->double('cost', 15, 2)->default('0.0');
            $table->double('price', 15, 2)->default('0.0');
            $table->double('ratting', 15, 2)->default('0.0');
            $table->string('category')->nullable();
            $table->integer('price_sales')->nullable();
            $table->integer('stock')->nullable();
            $table->string('weight')->nullable();
            $table->string('width_product')->nullable();
            $table->string('sku')->nullable();
            $table->string('maker')->nullable();
            $table->string('length_product')->nullable();
            $table->string('height_product')->nullable();
            $table->string('user_code')->nullable();
            $table->integer('type')->default(0);
            $table->integer('active')->default(0);
            $table->string('option1')->nullable();
            $table->string('option2')->nullable();
            $table->timestamps();
        });
    }

    /**option1
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
