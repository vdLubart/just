<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Lubart\Form\Form;
use Lubart\Form\FormGroup;
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
        'title', 'channels', 'orderNo', 'isActive'
    ];
    
    protected $table = 'contacts';


    protected $settingsTitle = 'Office';
    
    protected $neededParameters = [ 'channels' ];
    
    /**
     * Available contact fields.
     * Structure: <db column> => [<label>, <fa icon>]
     * 
     * @var array $fields
     */
    protected $defaultChannels = [
        'envelope'  => 'Address',
        'phone'     => 'Phone',
        'mobile'    => 'Mobile',
        'fax'       => 'Fax',
        'at'        => 'Email',
        'facebook'  => 'Facebook',
        'youtube'   => 'YouTube',
        'twitter'   => 'Twitter',
        'linkedin'  => 'LinkedIn',
        'github'    => 'GitHub',
        'google-plus' => 'Google Plus',
        'instagram' => 'Instagram',
        'pinterest' => 'Pinterest',
        'reddit'    => 'Reddit',
        'skype'     => 'Skype',
        'slack'     => 'Slack',
        'soundcloud' => 'SoundCloud',
        'telegram'  => 'Telegram',
        'viber'     => 'Viber',
        'vimeo'     => 'Vimeo',
        'whatsapp'  => 'WhatsApp',
    ];
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::text(['name'=>'title', 'label'=>'Office Title', 'value'=>@$this->title]));

        $channels = json_decode($this->channels);

        foreach($this->allChannels() as $icon=>$label){
            $this->form->add(FormElement::text(['name'=>$icon, 'label'=>$label, 'value'=>@$channels->{$icon}]));
        }
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        
        $this->form->setType('settings');
        
        return $this->form;
    }

    public function addSetupFormElements(Form &$form){

        $fieldGroup = new FormGroup('fieldGroup', 'Using contacts', ['class'=>'col-md-6']);

        foreach($this->defaultChannels() as $icon=> $label){
            $fieldGroup->add(FormElement::checkbox(['name'=>'channels[]', 'label'=>$label, 'value'=>$icon, 'check'=>(in_array($icon, $this->parameter('channels')??[]))]));
        }

        $form->addGroup($fieldGroup);

        if(\Auth::user()->role == 'master') {
            $additionalFieldGroup = new FormGroup('additionalFieldGroup', 'Additional contact', ['class' => 'col-md-6']);
            $additionalFieldGroup->add(FormElement::textarea(['name' => 'additionalFields', 'label' => 'Additional contact fileds (format: icon=>label)', 'value' => $this->parameter('additionalFields')]));

            $form->addGroup($additionalFieldGroup);
        }
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

        $contact->title = $request->get('title');
        $channels = new \stdClass();
        foreach($request->all() as $channel=>$val){
            if(in_array($channel, array_keys($this->allChannels()))) {
                $channels->{$channel} = $val;
            }
        }

        $contact->channels = json_encode($channels);
        
        $contact->save();
        
        $this->handleAddons($request, $contact);

        return $contact;
    }
    
    public static function defaultChannels() {
        $instance = new static;

        return $instance->defaultChannels;
    }

    /**
     * Combine default and additional channel lists
     *
     * @return array
     */
    public function allChannels() {
        $contacts = [];

        foreach($this->parameter('channels')??[] as $channel){
            $contacts[$channel] = $this->defaultChannels[$channel];
        }

        $additionalChannels = explode('\n', @$this->parameter('additionalFields'));

        if(!empty($additionalChannels[0])) {
            foreach ($additionalChannels as $channel) {
                $additional = explode('=>', $channel);

                $contacts[@$additional[0]] = $additional[1];
            }
        }

        return $contacts;
    }

    public function contacts() {
        $channels = json_decode($this->channels);
        $contacts = [];

        foreach($this->allChannels() as $icon=>$label){
            $contacts[$icon] = [
                'label' => $label,
                'value' => $channels->{$icon}
            ];
        }

        return $contacts;
    }

    public function contact($channel) {
        return $this->contacts()[$channel]['value'];
    }
}
