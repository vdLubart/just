<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lubart\Just\Database\Helpers\AddTranslations;
use Lubart\Just\Structure\Panel\Block\Contact;

class AddTranslationToContactsTable extends Migration
{
    use AddTranslations;

    protected $table = 'contacts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->json('channels')->change();
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
        Schema::table($this->table, function (Blueprint $table) {
            $table->text('channels')->change();
        });

        $this->convertBack(Contact::all(), ['title' => ['string', 255]]);
    }
}
