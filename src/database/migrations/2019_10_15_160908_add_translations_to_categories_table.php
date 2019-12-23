<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Just\Structure\Panel\Block\Addon\Categories;
use Just\Database\Helpers\AddTranslations;

class AddTranslationsToCategoriesTable extends Migration
{
    use AddTranslations;

    protected $table = 'categories';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->renameColumn('name', 'title');
        });

        $this->convertToJson(['title']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->convertBack(Categories::all(), ['title' => ['string', 255]]);

        Schema::table($this->table, function (Blueprint $table) {
            $table->renameColumn('title', 'name');
        });
    }
}
