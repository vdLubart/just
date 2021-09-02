<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table should be used by add-on like Comment
        // Note! Column for add-on this not created yet!
        Schema::create('comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->text("comment");
            $table->integer('user_id')->nullable()->default(null)->unsigned();
            $table->char("lang", 2);
            $table->timestamps();
            
            $table->foreign("user_id")->references("id")->on("users")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
