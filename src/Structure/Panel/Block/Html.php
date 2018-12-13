<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;

class Html extends Text
{
    
    protected $settingsTitle = 'HTML Code';
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>'Text', 'value'=>@$this->text]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        
        return $this->form;
    }
}
