<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Lubart\Just\Structure\Panel\Block\Contact;

class CreateContactsTable extends Migration
{
    protected $fields = [
        'address'   => ['Address', 'envelope'],
        'phone'     => ['Phone', 'phone'],
        'phone2'    => ['Mobile', 'mobile'],
        'fax'       => ['Fax', 'fax'],
        'email'     => ['Email', 'at'],
        'facebook'  => ['Facebook', 'facebook'],
        'youtube'   => ['YouTube', 'youtube'],
        'twitter'   => ['Twitter', 'twitter'],
        'linkedin'  => ['LinkedIn', 'linkedin'],
        'github'    => ['GitHub', 'github'],
        'google-plus' => ['Google Plus', 'google-plus'],
        'instagram' => ['Instagram', 'instagram'],
        'pinterest' => ['Pinterest', 'pinterest'],
        'reddit'    => ['Reddit', 'reddit'],
        'skype'     => ['Skype', 'skype'],
        'slack'     => ['Slack', 'slack'],
        'soundcloud' => ['SoundCloud', 'soundcloud'],
        'telegram'  => ['Telegram', 'telegram'],
        'viber'     => ['Viber', 'viber'],
        'vimeo'     => ['Vimeo', 'vimeo'],
        'whatsapp'  => ['WhatsApp', 'whatsapp']
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->string('title')->nullable();
            foreach($this->fields as $field=>$attr){
                $table->string($field)->nullable();
            }
            $table->integer('orderNo');
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            
            $table->foreign("block_id")->references("id")->on("blocks")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
