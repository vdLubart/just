<?php

namespace Lubart\Just\Structure\Panel\Block\Addon;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel\Block\Addon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class Images extends AbstractAddon
{
    protected $table = 'images';
    
    protected $fillable = ['addon_id', 'value'];
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Addon $addon Addon object
     * @param Form $form Form object
     */
    public static function updateForm(Addon $addon, Form $form, $values) {
        $image = is_null($values->where('addon_id', $addon->id)->first())?null:$values->where('addon_id', $addon->id)->first()->value;
        
        $form->add(FormElement::file(['name'=>$addon->name.'_'.$addon->id, 'label'=>$addon->title]));
        if(!is_null($image)){
            $modelTable = $values->first()->getRelations()['pivot']->pivotParent->getTable();
            $form->add(FormElement::html(['name'=>'addonImagePreview'.'_'.$addon->id, 'value'=>'<img src="/storage/'.$modelTable.'/'.$image.'_3.png" />']));
        }
        
        return $form;
    }
    
    public static function handleForm(Addon $addon, Request $request, $item) {
        if(!is_null($request->file($addon->name.'_'.$addon->id))){
            if(!file_exists(public_path('storage/'.$item->getTable()))){
                mkdir(public_path('storage/'.$item->getTable()), 0775);
            }
        
            $imageFile = Image::make($request->file($addon->name.'_'.$addon->id));
            
            $fileName = uniqid();
            
            $imageFile->encode('png')->save(public_path('storage/'.$item->getTable().'/'.$fileName.".png"));

            $item->multiplicateImage($fileName);
            
            $oldImage = Images::where(['addon_id'=>$addon->id])->first();
            if(!empty($oldImage)){
                $item->deleteImage($oldImage->value);
            }
            
            self::handleData($fileName, $addon, $item);
        }
    }
    
    public static function validationRules(Addon $addon) {
        return [
            $addon->name.'_'.$addon->id => "file|nullable",
        ];
    }
}
