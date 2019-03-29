<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaFieldToPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function(Blueprint $table){
            $table->string('keywords', 500)->after('description')->default('');
            $table->string('author', 200)->after('keywords')->default('');
            $table->string('copyright', 200)->after('author')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function(Blueprint $table){
            $table->dropColumn('keywords');
            $table->dropColumn('author');
            $table->dropColumn('copyright');
        });
    }
}
