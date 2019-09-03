<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;

class Html extends Text
{
    
    protected $settingsTitle = 'HTML Code';

    public function __construct() {
        $this->settingsTitle = __('html.title');
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>__('html.title'), 'value'=>@$this->text]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $this->form;
    }
}
