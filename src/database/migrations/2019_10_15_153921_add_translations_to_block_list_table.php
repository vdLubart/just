<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslationsToBlockListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blockList', function (Blueprint $table) {
            $table->dropIndex('blocklist_title_unique');

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
        Schema::table('blockList', function (Blueprint $table) {
            $table->string('title', 100)->unique()->after('block')->default(null)->nullable();
            $table->string('description')->after('title');
        });

        foreach(DB::table('blockList')->get() as $block){
            DB::table('blockList')
                ->where('block', $block->block)
                ->update([
                    'title' => __('block.list.'.$block->block.'.title'),
                    'description' => __('block.list.'.$block->block.'.description')
                ]);
        }
    }
}
