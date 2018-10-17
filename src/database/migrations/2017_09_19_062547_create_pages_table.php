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
            $table->string('title');
            $table->text('description')->nullable()->default(null);
            $table->string('route');
            $table->integer('layout_id')->unsigned();
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
