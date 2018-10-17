<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Table 'blockAttributes' contains some specific block setup attributes.
 * These attributes can be used for block customization and specifing some 
 * uncommon parameters
 */
class CreateBlockAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blockAttributes');
    }
}
