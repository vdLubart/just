<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->json("subject");
            $table->string('slug')->unique();
            $table->json("summary", 1000);
            $table->json("text");
            $table->string("image");
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
        Schema::dropIfExists('articles');
    }
}
