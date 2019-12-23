<?php

namespace Just\Structure\Panel\Block;

use Lubart\Form\FormElement;

class Html extends Text
{
    
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
