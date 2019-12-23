<?php

use Illuminate\Database\Migrations\Migration;
use Just\Database\Helpers\AddTranslations;
use Just\Structure\Panel\Block\Text;

class AddTranslationToTextsTable extends Migration
{
    use AddTranslations;

    protected $table = 'texts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['text']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Text::all(), ['text' => ['text']]);
    }
}
