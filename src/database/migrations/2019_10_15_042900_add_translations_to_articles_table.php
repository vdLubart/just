<?php

use Illuminate\Database\Migrations\Migration;
use Just\Structure\Panel\Block\Articles;
use Just\Database\Helpers\AddTranslations;

class AddTranslationsToArticlesTable extends Migration
{
    use AddTranslations;

    protected $table = 'articles';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['subject', 'summary', 'text']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Articles::all(), ['subject' => ['string', 255], 'summary'=>['string', 1000], 'text'=>['text']]);
    }
}
