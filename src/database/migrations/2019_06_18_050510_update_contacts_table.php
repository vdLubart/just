<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Just\Structure\Panel\Block\Contact;

class UpdateContactsTable extends Migration
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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('channels')->after('title')->default('{}');
        });

        $contacts = Contact::all();

        foreach($contacts as $i=>$contact){
            $channels = new stdClass();

            foreach($this->fields as $field=>$label){
                if(!empty($contact->{$field})) {
                    $channels->{$field} = $contact->{$field};
                }
            }

            $contact->channels = json_encode($channels);
            $contacts[$i] = $contact;
        }

        Schema::table('contacts', function (Blueprint $table) {
            foreach($this->fields as $field=>$attr){
                $table->dropColumn($field);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            foreach($this->fields as $field=>$attr){
                $table->string($field)->nullable();
            }
        });

        $contacts = Contact::all();

        foreach($contacts as $contact){
            $channels = json_decode($contact->channels);

            foreach($this->fields as $field=>$label){
                $contact->{$field} = $channels->{$label[1]} ?? '';
            }

            $contact->save();
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('channels');
        });
    }
}
