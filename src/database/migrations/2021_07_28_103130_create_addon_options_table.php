<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addonOptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('add_on_id')->unsigned();
            $table->json('option')->nullable();
            $table->integer('orderNo')->unsigned();
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->foreign("add_on_id")->references("id")->on("addons")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addonOptions');
    }
}
