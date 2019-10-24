<?php

use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block\Addon\Strings;

class AddTranslationToStringsTable extends Migration
{
    use AddTranslations;

    protected $table = 'strings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['value']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Strings::all(), ['value' => ['string', 255]]);
    }
}
