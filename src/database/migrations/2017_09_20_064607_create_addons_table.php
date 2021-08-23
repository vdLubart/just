<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->string('type');
            $table->string('name');
            $table->json('title');
            $table->json('description')->nullable();
            $table->integer('orderNo')->unsigned();
            $table->boolean('isActive')->default(true);
            $table->boolean('isRequired')->default(false);
            $table->string('parameters')->default('{}');
            $table->timestamps();

            $table->foreign('type')->references('addon')->on('addonList')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('addons');
    }
}
