<?php

use Illuminate\Database\Migrations\Migration;
use Just\Database\Helpers\AddTranslations;
use Just\Structure\Panel\Block\Menu;

class AddTranslationsToMenusTable extends Migration
{
    use AddTranslations;

    protected $table = 'menus';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['item']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Menu::all(), ['item' => ['string', 255]]);
    }
}
