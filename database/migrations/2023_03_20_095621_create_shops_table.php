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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name_shop')->nullable();
            $table->text('detail_shop')->nullable();
            $table->string('img_shop')->nullable();
            $table->string('cover_img_shop')->nullable();
            $table->string('ratting')->nullable();
            $table->string('user_code')->nullable();
            $table->string('code_shop')->nullable();
            $table->text('url_shop')->nullable();
            $table->integer('user_id')->default('0');
            $table->integer('type')->default('0');
            $table->integer('theme')->default('0');
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
        Schema::dropIfExists('shops');
    }
};
