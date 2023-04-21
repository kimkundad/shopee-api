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
        Schema::create('categorys_shop', function (Blueprint $table) {
            $table->id();
            $table->string('category_img', 199)->nullable()->comment("รูปของ category");
            $table->string('category_name', 191)->nullable()->comment("ชื่อของ category");
            $table->integer('shop_id')->nullable();
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
        Schema::dropIfExists('categorys_shop');
    }
};
