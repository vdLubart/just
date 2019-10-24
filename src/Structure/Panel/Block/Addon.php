<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Lubart\Just\Models\AddonList;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Support\Facades\DB;
use Lubart\Form\FormElement;
use Lubart\Just\Tools\Useful;
use Illuminate\Support\Facades\Artisan;
use Lubart\Just\Requests\AddonChangeRequest;
use Spatie\Translatable\HasTranslations;

class Addon extends Model
{
    use HasTranslations;

    protected $table = 'addons';
    
    protected $fillable = ['block_id', 'type', 'name', 'title', 'description', 'orderNo', 'isActive', 'parameters'];

    public $translatable = ['title', 'description'];
    
    /**
     * Class name of the current addon
     * 
     * @return string $addon
     */
    public function addon(){
        $class = "\\Lubart\\Just\\Structure\\Panel\\Block\\Addon\\".ucfirst($this->type);
        
        if(!class_exists($class)){
            $class = "\\App\\Just\\Panel\\Block\\Addon\\". ucfirst($this->type);
            if(!class_exists($class)){
                throw new \Exception("Addon class not found");
            }
        }
        
        return $class;
    }
    
    /**
     * Block belongs to the current addon
     * 
     * @return Block
     */
    public function block() {
        return $this->belongsTo(Block::class);
    }
    
    /**
     * Update existing settings form and add new elements
     * 
     * @param Form $form Form object
     * @param mixed $values element values
     */
    public function updateForm(Form $form, $values) {
        return call_user_func([$this->addon(), "updateForm"], $this, $form, $values);
    }
    
    /**
     * Treat form elements related to the addon
     * 
     * @param Request $request
     * @param mixed $item Model item
     * @return type
     */
    public function handleForm(Request $request, $item) {
        return call_user_func([$this->addon(), "handleForm"], $this, $request, $item);
    }
    
    public function validationRules() {
        return call_user_func([$this->addon(), "validationRules"], $this);
    }
    
    public function values() {
        return $this->hasMany($this->addon());
    }
    
    public function valuesSelectArray($value, $key='id') {
        $values = [];
        foreach($this->values as $val){
            $values[$val->$key] = $val->$value;
        }
        
        return $values;
    }
    
    public function createPivotTable($modelTable, $addonTable) {
        if(!Schema::hasTable($modelTable."_".$addonTable)){
            Artisan::call("make:addonMigration", ["name" => "create_".$modelTable."_".$addonTable."_table"]);
            
            Artisan::call("migrate", ["--step" => true]);
        }
    }
    
    /**
     * Get page settings form
     * 
     * @return Form
     */
    public function settingsForm() {
        $form = new Form('admin/settings/addon/setup');
        
        $addons = [];
        foreach(AddonList::all() as $addon){
            $addons[$addon->addon] = __('addon.list.'.$addon->addon.'.title') ." - ".__('addon.list.'.$addon->addon.'.description');
        }
        $blocks = [];
        foreach(Block::all() as $block){
            $blocks[$block->id] = $block->title . "(".$block->type.") at ".(is_null($block->page())?$block->panelLocation:$block->page()->title ." page");
        }
        
        $form->add(FormElement::hidden(['name'=>'addon_id', 'value'=>@$this->id]));
        $form->add(FormElement::select(['name'=>'type', 'label'=>__('addon.addForm.addon'), 'value'=>@$this->type, 'options'=>$addons]));
        $form->add(FormElement::text(['name'=>'name', 'label'=>__('settings.common.name'), 'value'=>@$this->name]));
        $form->add(FormElement::select(['name'=>'block_id', 'label'=>__('addon.addForm.block'), 'value'=>@$this->block_id, 'options'=>$blocks]));
        if(!is_null($this->id)){
            $form->getElement("type")->setParameters("disabled", "disabled");
            $form->getElement("block_id")->setParameters("disabled", "disabled");
        }
        $form->add(FormElement::text(['name'=>'title', 'label'=>__('settings.common.title'), 'value'=>@$this->title]));
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>@$this->description, 'id'=>'description']));
        $form->applyJS("CKEDITOR.replace('description');");
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
    
    public function handleSettingsForm(AddonChangeRequest $request) {
        $this->title = $request->title;
        $this->description = $request->description;
        $this->name = $request->name;
        
        if(is_null($this->id)){
            $this->block_id = $request->block_id;
            $this->type = $request->type;
            $this->orderNo = Useful::getMaxNo('addons', ['block_id'=>$request->block_id]);
            
            $modelTable = Block::find($request->block_id)->specify()->model()->getTable();
            $addonTable = AddonList::where('addon', $request->type)->first()->table;
            
            $this->createPivotTable($modelTable, $addonTable);
        }
        
        $this->save();
    }
    
    public function delete() {
        if(in_array($this->type, ['images'])){
            $imagesInBlock = $this->block->specify()->content();
            foreach ($imagesInBlock as $item){
                $item->deleteImage($item->{$this->name});
            }
        }
        
        parent::delete();
    }
}
