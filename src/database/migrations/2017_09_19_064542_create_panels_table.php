<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->string('location');
            $table->integer('layout_id')->unsigned()->nullable()->default(null);
            $table->enum('type', ['static', 'dynamic']);
            $table->integer('orderNo');
            $table->timestamps();
            
            $table->foreign('layout_id')->references('id')->on('layouts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('location')->references('location')->on('panelLocations')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panels');
    }
}
