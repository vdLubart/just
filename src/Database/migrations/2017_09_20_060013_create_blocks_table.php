<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->string('type');
            $table->string('name')->unique()->nullable()->default(null);
            $table->string('panelLocation')->nullable()->default(null);
            $table->integer('page_id')->unsigned()->nullable()->default(null);
            $table->json('title');
            $table->json('description');
            $table->integer('width')->unsigned()->default(12);
            $table->string('layoutClass', 100)->default('primary');
            $table->string('cssClass', 200)->nullable()->default(null);
            $table->integer('orderNo')->unsigned();
            $table->boolean('isActive')->default(true);
            $table->json('parameters');
            $table->integer('parent')->unsigned()->nullable()->default(null);
            $table->timestamps();
            
            $table->foreign('type')->references('block')->on('blockList')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('panelLocation')->references('location')->on('panelLocations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('pages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('parent')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade');
        });
        
        Schema::table('routes', function($table){
           $table->foreign('block_id')->references('id')->on('blocks')->onUpdate('cascade')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function($table){
           $table->dropForeign('routes_block_id_foreign'); 
        });
        
        Schema::dropIfExists('blocks');
    }
}
