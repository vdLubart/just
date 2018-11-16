<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->boolean('isActive')->default(0);
            $table->timestamps();
        });
        
        Schema::table('layouts', function(Blueprint $table){
            $table->foreign('name')->references('name')->on('themes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('layouts', function(Blueprint $table){
            $table->dropForeign('layouts_name_foreign');
        });
        
        Schema::dropIfExists('themes');
    }
}
