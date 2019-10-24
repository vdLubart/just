<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddTranslationsToAddonListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addonList', function (Blueprint $table) {
            $table->dropIndex('addonlist_title_unique');

            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addonList', function (Blueprint $table) {
            $table->string('title', 100)->unique()->after('addon')->default(null)->nullable();
            $table->string('description')->after('title');
        });

        foreach(DB::table('addonList')->get() as $addon){
            DB::table('addonList')
                ->where('addon', $addon->addon)
                ->update([
                    'title' => __('addon.list.'.$addon->addon.'.title'),
                    'description' => __('addon.list.'.$addon->addon.'.description')
            ]);
        }
    }
}
