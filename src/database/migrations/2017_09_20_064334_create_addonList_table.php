<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addonList', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->string('addon')->unique();
            $table->char('title', 100)->unique();
            $table->text('description');
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
        Schema::dropIfExists('addonList');
    }
}
