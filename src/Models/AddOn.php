<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Just\Models\System\AddonList;
use Lubart\Form\FormElement;
use Just\Tools\Useful;
use Illuminate\Support\Facades\Artisan;
use Just\Requests\AddonChangeRequest;
use Spatie\Translatable\HasTranslations;
use Lubart\Form\FormGroup;

class AddOn extends Model
{
    use HasTranslations;

    protected $table = 'addons';
    
    protected $fillable = ['block_id', 'type', 'name', 'title', 'description', 'orderNo', 'isActive', 'parameters'];

    public $translatable = ['title', 'description'];
    
    /**
     * Class name of the current addon
     * 
     * @return string $addon
     * @throws \Exception
     */
    public function addon(){
        $class = "\\Just\\Models\\AddOns\\".ucfirst($this->type);
        
        if(!class_exists($class)){
            $class = "\\App\\Just\\Models\\AddOns\\". ucfirst($this->type);
            if(!class_exists($class)){
                throw new \Exception("Add-on class not found");
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
            $addons[$addon->addon] = __('addOn.list.'.$addon->addon.'.title') ." - ".__('addOn.list.'.$addon->addon.'.description');
        }
        $blocks = [];
        foreach(Block::all() as $block){
            $blocks[$block->id] = $block->title . "(".$block->type.") at ".(is_null($block->page())?$block->panelLocation:$block->page()->title ." page");
        }
        
        $form->add(FormElement::hidden(['name'=>'addon_id', 'value'=>@$this->id]));
        $form->add(FormElement::select(['name'=>'type', 'label'=>__('addOn.createForm.addOn'), 'value'=>@$this->type, 'options'=>$addons])
            ->obligatory()
        );
        $form->add(FormElement::text(['name'=>'name', 'label'=>__('addOn.createForm.name'), 'value'=>@$this->name])
            ->obligatory()
        );
        $form->add(FormElement::select(['name'=>'block_id', 'label'=>__('addOn.createForm.block'), 'value'=>@$this->block_id, 'options'=>$blocks])
            ->obligatory()
        );
        if(!is_null($this->id)){
            $form->element("type")->setParameters("disabled", "disabled");
            $form->element("block_id")->setParameters("disabled", "disabled");
        }
        $form->add(FormElement::text(['name'=>'title', 'label'=>__('addOn.createForm.title'), 'value'=>@$this->title]));
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>@$this->description]));

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
