<?php

use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block;

class AddTranslationsToBlocksTable extends Migration
{
    use AddTranslations;

    protected $table = 'blocks';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['title', 'description']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Block::all(), ['title' => ['string', 255], 'description'=>['text']]);
    }
}
