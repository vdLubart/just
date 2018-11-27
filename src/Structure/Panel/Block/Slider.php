<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;

class Slider extends Gallery
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orderNo',
    ];
    
    protected $table = 'photos';
    
    protected $settingsTitle = 'Slide';
    
    protected $neededParameters = [
        'cropDimentions'     => 'Crop slide to side (W:H)'
    ];
    
    protected $imageSizes = [];
    
    public function form() {
        $this->form = parent::form();
        
        $this->form->useJSFile('/js/blocks/slider/settingsForm.js');
        
        return $this->form;
    }
    
    public function addSetupFormElements(Form &$form) {
        $form->add(FormElement::hidden(['name'=>'cropPhoto', 'value'=>1]));
        $form->add(FormElement::checkbox(['name'=>'ignoreCaption', 'label' => 'Ignore item caption', 'value'=>1, 'check'=>$this->parameter('ignoreCaption')]));
        $form->add(FormElement::checkbox(['name'=>'ignoreDescription', 'label' => 'Ignore item description', 'value'=>1, 'check'=>$this->parameter('ignoreDescription')]));
        
        return $form;
    }
}
