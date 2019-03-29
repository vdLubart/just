<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Models\Theme;
use Lubart\Just\Structure\Layout;

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
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->string('name')->unique();
            $table->boolean('isActive')->default(0);
            $table->timestamps();
        });
        
        $layoutNames = Layout::distinct()->groupBy('name')->get(['name']);
        
        foreach($layoutNames as $layout){
            Theme::create(['name'=>$layout->name]);
        }
        
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
