<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;

class Twitter extends AbstractBlock
{
    
    protected $neededParameters = ['account', 'widgetId'];
    
    protected $settingsTitle = 'Twitter';
    
    public function content($id = null) {
        return;
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        return $this->form;
    }

    public function addSetupFormElements(Form &$form){
        $twitterGroup = new FormGroup('twitterGroup', 'Account Settings', ['class'=>'col-md-6']);
        $twitterGroup->add(FormElement::text(['name'=>'account', 'label'=>'Account name', 'value'=>@$this->parameter('account')]));
        $twitterGroup->add(FormElement::text(['name'=>'widgetId', 'label'=>'Widget ID', 'value'=>@$this->parameter('widgetId')]));
        $form->addGroup($twitterGroup);

        return $form;
    }
}
