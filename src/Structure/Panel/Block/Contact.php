<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormGroup;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Lubart\Just\Tools\Useful;
use Lubart\Form\FormElement;
use Spatie\Translatable\HasTranslations;

class Contact extends AbstractBlock
{
    use HasTranslations;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'orderNo', 'isActive'
    ];

    public $translatable = ['title'];

    protected $casts = [
        'channels' => 'object'
    ];
    
    protected $table = 'contacts';

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

    public function __construct() {
        parent::__construct();

        $this->defaultChannels['envelope'] = __('contact.channel.address');
        $this->defaultChannels['phone'] = __('contact.channel.phone');
        $this->defaultChannels['mobile'] = __('contact.channel.mobile');
        $this->defaultChannels['fax'] = __('contact.channel.fax');
    }

    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::text(['name'=>'title', 'label'=>__('contact.office'), 'value'=>@$this->title]));

        foreach($this->allChannels() as $icon=>$label){
            $this->form->add(FormElement::text(['name'=>$icon, 'label'=>$label, 'value'=>@$this->channels->{$icon}]));
        }
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        $this->form->setType('settings');
        
        return $this->form;
    }

    public function addSetupFormElements(Form &$form){

        $fieldGroup = new FormGroup('fieldGroup', __('contact.preferences.usingContacts'), ['class'=>'col-md-6']);

        foreach($this->defaultChannels() as $icon=> $label){
            $fieldGroup->add(FormElement::checkbox(['name'=>'channels[]', 'label'=>$label, 'value'=>$icon, 'check'=>(in_array($icon, $this->parameter('channels')??[]))]));
        }

        $form->addGroup($fieldGroup);

        if(\Auth::user()->role == 'master') {
            $additionalFieldGroup = new FormGroup('additionalFieldGroup', __('contact.preferences.additionalContact'), ['class' => 'col-md-6']);
            $additionalFieldGroup->add(FormElement::textarea(['name' => 'additionalFields', 'label' => __('contact.preferences.additionalContactFormat'), 'value' => $this->parameter('additionalFields')]));

            $form->addGroup($additionalFieldGroup);
        }
    }
    
    public function handleForm(ValidateRequest $request) {
        if (is_null($request->id)) {
            $contact = new Contact;
            $contact->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->block_id]);
            $contact->setBlock($request->block_id);
        }
        else{
            $contact = Contact::findOrNew($request->id);
        }

        $contact->title = $request->title;
        $channels = new \stdClass();
        foreach($request->all() as $channel=>$val){
            if(in_array($channel, array_keys($this->allChannels()))) {
                $channels->{$channel} = $val;
            }
        }

        $contact->channels = $channels;
        
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

        $additionalChannels = explode("\n", @$this->parameter('additionalFields'));

        if(!empty($additionalChannels[0])) {
            foreach ($additionalChannels as $channel) {
                $additional = explode('=>', $channel);

                $contacts[@$additional[0]] = $additional[1];
            }
        }

        return $contacts;
    }

    public function contacts() {
        $contacts = [];

        foreach($this->allChannels() as $icon=>$label){
            $contacts[$icon] = [
                'label' => $label,
                'value' => @$this->channels->{$icon}
            ];
        }

        return $contacts;
    }

    public function contact($channel) {
        return $this->contacts()[$channel]['value'];
    }
}
