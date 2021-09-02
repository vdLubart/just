<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->string('image')->nullable()->default(null);
            $table->string('video')->nullable()->default(null);
            $table->string('externalUrl', 1000)->nullable()->default(null);
            $table->json('caption');
            $table->json('description');
            $table->integer('orderNo')->unsigned();
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->foreign("block_id")->references("id")->on("blocks")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photos');
    }
}
