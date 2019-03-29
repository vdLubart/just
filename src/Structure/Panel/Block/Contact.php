<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Lubart\Just\Tools\Useful;
use Lubart\Form\FormElement;

class Contact extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption', 'description'
    ];
    
    protected $table = 'contacts';
    
    protected $settingsTitle = 'Office';
    
    protected $neededParameters = [];
    
    /**
     * Available contact fields.
     * Structure: <db column> => [<label>, <fa icon>]
     * 
     * @var array $fields
     */
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
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::text(['name'=>'title', 'label'=>'Office Title', 'value'=>@$this->title]));
        foreach($this->fields as $field=>$attr){
            $this->form->add(FormElement::text(['name'=>$field, 'label'=>$attr[0], 'value'=>@$this->{$field}]));
        }
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        
        $this->form->setType('settings');
        
        return $this->form;
    }
    
    public function handleForm(Request $request) {
        if (is_null($request->get('id'))) {
            $contact = new Contact;
            $contact->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
            $contact->setBlock($request->get('block_id'));
        }
        else{
            $contact = Contact::findOrNew($request->get('id'));
        }
        
        foreach($request->all() as $field=>$val){
            if(!in_array($field, ['_token', 'submit'])){
                $contact->{$field} = $val;
            }
        }
        
        $contact->save();
        
        $this->handleAddons($request, $contact);

        return $contact;
    }
    
    public static function fields() {
        $instance = new static;
        
        return $instance->fields;
    }
}
