<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->string('subject');
            $table->string('summary');
            $table->string('slug');
            $table->string('text');
            $table->string('image');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location');
            $table->boolean('isActive')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('events');
    }
}
