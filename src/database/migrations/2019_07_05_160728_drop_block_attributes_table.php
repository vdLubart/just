<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBlockAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('blockAttributes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('blockAttributes', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('label');
            $table->string('name');
            $table->string('defaultValue');
            $table->string('block');
            $table->timestamps();

            $table->foreign("block")->references("block")->on("blockList")->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
