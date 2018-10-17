<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockList', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->string('block')->unique();
            $table->char('title', 100)->unique();
            $table->char('description', 255);
            $table->string('table', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blockList');
    }
}
