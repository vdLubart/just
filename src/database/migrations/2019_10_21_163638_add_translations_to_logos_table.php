<?php

use Illuminate\Database\Migrations\Migration;
use Just\Database\Helpers\AddTranslations;
use Just\Structure\Panel\Block\Logo;

class AddTranslationsToLogosTable extends Migration
{
    use AddTranslations;

    protected $table = 'logos';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->convertToJson(['caption', 'description']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Logo::all(), ['caption' => ['string', 255], 'description'=>['text']]);
    }
}
