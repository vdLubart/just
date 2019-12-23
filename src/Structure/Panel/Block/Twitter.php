<?php

namespace Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class Twitter extends AbstractBlock
{
    
    protected $neededParameters = ['account', 'widgetId'];
    
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
        $twitterGroup = new FormGroup('twitterGroup', __('twitter.preferences.title'), ['class'=>'col-md-6']);
        $twitterGroup->add(FormElement::text(['name'=>'account', 'label'=>__('twitter.preferences.account'), 'value'=>@$this->parameter('account')]));
        $twitterGroup->add(FormElement::text(['name'=>'widgetId', 'label'=>__('twitter.preferences.widgetId'), 'value'=>@$this->parameter('widgetId')]));
        $form->addGroup($twitterGroup);

        return $form;
    }

    public function handleForm(ValidateRequest $request) {
        return;
    }
}
