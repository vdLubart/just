<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->json('title')->nullable()->default(null);
            $table->json('description')->nullable()->default(null);
            $table->string('keywords', 500)->nullable()->default(null);
            $table->json('author')->nullable()->default(null);
            $table->json('copyright')->nullable()->default(null);
            $table->string('route');
            $table->integer('layout_id')->unsigned();
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->foreign('route')->references('route')->on('routes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('layout_id')->references('id')->on('layouts')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
