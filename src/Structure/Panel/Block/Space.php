<?php

namespace Just\Structure\Panel\Block;

use Illuminate\Database\Eloquent\Collection;
use Just\Structure\Panel\Block;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class Space extends AbstractBlock
{
    protected $neededParameters = [
        'height'    => 'Block height'
    ];
    
    public function content() {
        return new Collection();
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        return $this->form;
    }
    
    /**
     * Return setup form for the current block
     * 
     * @param Block $block
     * @return Form
     */
    public function setupForm(Block $block) {
        $parameters = $block->parameters;
        
        $form = new Form('/admin/settings/setup');
        
        $form->setType('setup');
        
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$block->id]));
        foreach($this->neededParameters() as $param=>$label){
            $form->add(FormElement::text(['name'=>$param, 'label'=>__('space.'.$param), 'value'=>@$parameters->{$param}]));
        }
        
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }

    public function handleForm(ValidateRequest $request) {
        return ;
    }
}
