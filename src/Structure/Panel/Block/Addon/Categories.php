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
        DB::table($item->getTable()."_".$addon->type)
                ->where('modelItem_id', $item->id)
                ->delete();
        
        if(is_array($request->get($addon->name))){
            foreach($request->get($addon->name) as $cat){
                DB::table($item->getTable() . "_" . $addon->type)
                        ->insert([
                            'modelItem_id' => $item->id,
                            'addonItem_id' => $cat
                ]);
            }
        }
        else{
            DB::table($item->getTable()."_".$addon->type)
                    ->insert([
                        'modelItem_id' => $item->id,
                        'addonItem_id' => $request->get($addon->name)
                    ]);
        }
    }
    
    public static function validationRules(Addon $addon) {
        return [
            $addon->name => "required|integer|min:1",
        ];
    }
    
    /**
     * Get page settings form
     * 
     * @return Form
     */
    public function settingsForm() {
        $form = new Form('admin/settings/category/setup');
        
        $addons = Addon::where('type', 'categories')->pluck('title', 'id');
        
        $form->add(FormElement::hidden(['name'=>'category_id', 'value'=>@$this->id]));
        $form->add(FormElement::select(['name'=>'addon_id', 'label'=>'Addon', 'value'=>@$this->addon_id, 'options'=>$addons]));
        $form->add(FormElement::text(['name'=>'name', 'label'=>'Name', 'value'=>@$this->name]));
        $form->add(FormElement::text(['name'=>'value', 'label'=>'Value', 'value'=>@$this->value]));
        
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function handleSettingsForm(Request $request) {
        $this->addon_id = $request->addon_id;
        $this->name = $request->name;
        $this->value = $request->value;
        
        $this->save();
    }
}
