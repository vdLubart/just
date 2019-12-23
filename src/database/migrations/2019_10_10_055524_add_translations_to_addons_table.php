<?php

use Illuminate\Database\Migrations\Migration;
use Just\Structure\Panel\Block\Addon;
use Just\Database\Helpers\AddTranslations;

class AddTranslationsToAddonsTable extends Migration
{
    use AddTranslations;

    protected $table = 'addons';

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
        $this->convertBack(Addon::all(), ['title' => ['string', 255], 'description'=>['text']]);
    }
}
