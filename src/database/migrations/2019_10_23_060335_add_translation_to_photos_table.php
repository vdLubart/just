<?php

use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block\Gallery;

class AddTranslationToPhotosTable extends Migration
{
    use AddTranslations;

    protected $table = 'photos';

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
        $this->convertBack(Gallery::all(), ['caption' => ['string', 255], 'description' => ['text']]);
    }
}
