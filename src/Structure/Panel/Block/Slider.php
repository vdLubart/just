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
    
    protected $neededParameters = [];
    
    protected $imageSizes = [];
    
    public function form() {
        $this->form = parent::form();
        
        $this->form->useJSFile('/js/blocks/slider/settingsForm.js');
        
        return $this->form;
    }
    
    public function addSetupFormElements(Form &$form) {
        $this->addCropSetupGroup($form);
        
        $this->addIgnoretCaptionSetupGroup($form);
        
        $this->addResizePhotoSetupGroup($form);
        
        $form->useJSFile('/js/blocks/setupForm.js');
        
        return $form;
    }
}
