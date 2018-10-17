<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Form\Form;
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
    
    public function content($id = null) {
        if(is_null($id)){
            $content = $this->orderBy('orderNo')
                    ->where('block_id', $this->block_id);
            if(!\Config::get('isAdmin')){
                $content = $content->where('isActive', 1);
            }
            
            return $content->get();
        }
        else{
            return $this->find($id);
        }
    }
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->form->add(FormElement::text(['name'=>'title', 'label'=>'Office Title', 'value'=>@$this->title]));
        foreach($this->fields as $field=>$attr){
            $this->form->add(FormElement::text(['name'=>$field, 'label'=>$attr[0], 'value'=>@$this->{$field}]));
        }
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        
        $this->form->setType('settings');
        
        return $this->form;
    }
    /*
    public function setupForm(Block $block) {
        $parameters = json_decode($block->parameters);
        
        $form = new Form('/admin/settings/setup');
        
        $form->setType('setup');
        
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$block->id]));
        
        $form->add(FormElement::checkbox(['name'=>'cropPhoto', 'label'=>'Crop photo', 'value'=>1, 'check'=>(@$parameters->cropPhoto==1)]));
        $form->add(FormElement::text(['name'=>'cropDimentions', 'label'=>'Crop image with dimentions (W:H)', 'value'=>isset($parameters->cropDimentions)?$parameters->cropDimentions:'4:3']));
        $form->add(FormElement::submit(['value'=>'Save']));
        
        $form->useJSLogic();
        
        return $form;
    }
    */
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

        return $contact;
    }
    
    public static function fields() {
        $instance = new static;
        
        return $instance->fields;
    }
}
