<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addons', function (Blueprint $table) {
            $table->dropForeign('addons_name_foreign');
            $table->renameColumn('name', 'type');
        });
        
        Schema::table('addons', function (Blueprint $table) {
            $table->foreign('type')->references('addon')->on('addonList')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->after('type');
        });
        
        DB::table('addons')->update(['name' => DB::raw("concat(`type`, '_', `id`)")]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addons', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropForeign('addons_type_foreign');
        });
        
        Schema::table('addons', function (Blueprint $table) {
            $table->renameColumn('type', 'name');
            
            $table->foreign('name')->references('addon')->on('addonList')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
