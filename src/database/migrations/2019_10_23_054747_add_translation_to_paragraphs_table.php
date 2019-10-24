<?php

use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block\Addon\Paragraphs;

class AddTranslationToParagraphsTable extends Migration
{
    use AddTranslations;

    protected $table = 'paragraphs';

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
        $this->convertBack(Paragraphs::all(), ['value' => ['text']]);
    }
}
