<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer("block_id")->unsigned();
            $table->string("item");
            $table->integer('parent')->unsigned()->nullable()->default(null);
            $table->string('route')->nullable()->default(null);
            $table->string('url')->nullable()->default(null);
            $table->integer('orderNo');
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            
            $table->foreign("block_id")->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign("parent")->references('id')->on('menus')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('route')->references('route')->on('routes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
