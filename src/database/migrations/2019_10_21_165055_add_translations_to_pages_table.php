<?php

use Illuminate\Database\Migrations\Migration;
use Just\Database\Helpers\AddTranslations;
use Just\Models\Page;

class AddTranslationsToPagesTable extends Migration
{
    use AddTranslations;

    protected $table = 'pages';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['title', 'description', 'author', 'copyright']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Page::all(), ['title' => ['string', 255], 'description' => ['text'], 'author' => ['string', 200], 'copyright' => ['string', 200]]);
    }
}
