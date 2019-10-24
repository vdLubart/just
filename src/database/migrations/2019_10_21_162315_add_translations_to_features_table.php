<?php

use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block\Features;

class AddTranslationsToFeaturesTable extends Migration
{
    use AddTranslations;

    protected $table = 'features';

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
        $this->convertBack(Features::all(), ['title' => ['string', 255], 'description'=>['text']]);
    }
}
