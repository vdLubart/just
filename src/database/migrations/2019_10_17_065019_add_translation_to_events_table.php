<?php

use Illuminate\Database\Migrations\Migration;
use Just\Database\Helpers\AddTranslations;
use Just\Structure\Panel\Block\Events;

class AddTranslationToEventsTable extends Migration
{
    use AddTranslations;

    protected $table = 'events';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['subject', 'summary', 'text', 'location']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Events::all(), ['subject' => ['string', 255], 'summary'=>['string', 1000], 'text'=>['text'], 'location'=>['string', 255]]);
    }
}
