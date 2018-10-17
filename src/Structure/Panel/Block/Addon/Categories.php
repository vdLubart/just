<?php

namespace Lubart\Just\Structure\Panel\Block\Addon;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel\Block\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Categories extends AbstractAddon
{
    protected $table = 'categories';
    
    protected $fillable = ['addon_id', 'name'];
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Addon $addon Addon object
     * @param Form $form Form object
     */
    public static function updateForm(Addon $addon, Form $form, $values) {
        $form->add(FormElement::select(['name'=>$addon->name, 'label'=>$addon->title, 'options'=>$addon->valuesSelectArray('name'), 'value'=>$values]));
        
        return $form;
    }
    
    public static function handleForm(Addon $addon, Request $request, $item) {
        DB::table($item->getTable()."_".$addon->name)
                ->where('modelItem_id', $item->id)
                ->delete();
        
        if(is_array($request->get('categories'))){
            foreach($request->get('categories') as $cat){
                DB::table($item->getTable() . "_" . $addon->name)
                        ->insert([
                            'modelItem_id' => $item->id,
                            'addonItem_id' => $cat
                ]);
            }
        }
        else{
            DB::table($item->getTable()."_".$addon->name)
                    ->insert([
                        'modelItem_id' => $item->id,
                        'addonItem_id' => $request->get('categories')
                    ]);
        }
    }
    
    public static function validationRules(Addon $addon) {
        return [
            $addon->name => "required|integer|min:1",
        ];
    }
}
