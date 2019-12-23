<?php

namespace Just\Structure\Panel\Block\Addon;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Structure\Panel\Block\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class Categories extends AbstractAddon
{
    use HasTranslations;

    protected $table = 'categories';
    
    protected $fillable = ['addon_id', 'title', 'value'];

    public $translatable = ['title'];
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Addon $addon Addon object
     * @param Form $form Form object
     */
    public static function updateForm(Addon $addon, Form $form, $values) {
        $form->add(FormElement::select(['name'=>$addon->name."_".$addon->id, 'label'=>$addon->title, 'options'=>$addon->valuesSelectArray('title'), 'value'=>$values]));
        
        return $form;
    }
    
    public static function handleForm(Addon $addon, Request $request, $item) {
        DB::table($item->getTable()."_".$addon->type)
                ->join($addon->type, $item->getTable()."_".$addon->type.".addonItem_id", "=", $addon->type.".id")
                ->where('addon_id', $addon->id)
                ->where('modelItem_id', $item->id)
                ->delete();
        
        if(is_array($request->get($addon->name."_".$addon->id))){
            foreach($request->get($addon->name."_".$addon->id) as $cat){
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
                        'addonItem_id' => $request->get($addon->name."_".$addon->id)
                    ]);
        }
    }
    
    public static function validationRules(Addon $addon) {
        return [
            $addon->name."_".$addon->id => "required|integer|min:1",
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
        $form->add(FormElement::select(['name'=>'addon_id', 'label'=>__('addon.category.createForm.addon'), 'value'=>@$this->addon_id, 'options'=>$addons]));
        $form->add(FormElement::text(['name'=>'title', 'label'=>__('settings.common.title'), 'value'=>@$this->title]));
        $form->add(FormElement::text(['name'=>'value', 'label'=>__('addon.category.createForm.value'), 'value'=>@$this->value]));
        
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
    
    public function handleSettingsForm(Request $request) {
        $this->addon_id = $request->addon_id;
        $this->title = $request->title;
        $this->value = $request->value;
        
        $this->save();
    }
}
