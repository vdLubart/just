<?php

namespace Lubart\Just\Structure\Panel\Block\Addon;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel\Block\Addon;
use Illuminate\Http\Request;

class Strings extends AbstractAddon
{
    protected $table = 'strings';
    
    protected $fillable = ['addon_id', 'value'];
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Addon $addon Addon object
     * @param Form $form Form object
     */
    public static function updateForm(Addon $addon, Form $form, $values) {
        $form->add(FormElement::text(['name'=>$addon->name."_".$addon->id, 'label'=>$addon->title, 'value'=>(is_null($values->where('addon_id', $addon->id)->first())?"":$values->where('addon_id', $addon->id)->first()->value)]));
        
        return $form;
    }
    
    public static function handleForm(Addon $addon, Request $request, $item) {
        self::handleData($request->get($addon->name."_".$addon->id), $addon, $item);
    }
    
    public static function validationRules(Addon $addon) {
        return [
            $addon->name.'_'.$addon->id => "nullable",
        ];
    }
}
