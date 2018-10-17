<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;

/**
 * @method Lubart\Form\Form form() Settings form is described in Gallery
 */
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
    
    public function addSetupFormElements(Form &$form) {
        $form->add(FormElement::hidden(['name'=>'cropPhoto', 'value'=>1]));
        
        return $form;
    }
}
