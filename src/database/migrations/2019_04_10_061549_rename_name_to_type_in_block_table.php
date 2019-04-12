<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameNameToTypeInBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign('blocks_name_foreign');
            $table->renameColumn('name', 'type');
            
            $table->foreign('type')->references('block')->on('blockList')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign('blocks_type_foreign');
            $table->renameColumn('type', 'name');
            
            $table->foreign('name')->references('block')->on('blockList')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
