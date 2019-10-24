<?php

namespace Lubart\Just\Structure\Panel\Block\Addon;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel\Block\Addon;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;


class Paragraphs extends AbstractAddon
{
    use HasTranslations;

    protected $table = 'paragraphs';
    
    protected $fillable = ['addon_id', 'value'];

    public $translatable = ['value'];
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Addon $addon Addon object
     * @param Form $form Form object
     * @return Form
     */
    public static function updateForm(Addon $addon, Form $form, $values) {
        $form->add(FormElement::textarea(['name'=>$addon->name."_".$addon->id, 'label'=>$addon->title, 'value'=>(is_null($values->where('addon_id', $addon->id)->first())?"":$values->where('addon_id', $addon->id)->first()->value)]));
        
        $form->applyJS("$(document).ready(function(){CKEDITOR.replace('".$addon->name."_".$addon->id."');});");
        
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
