<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Requests\CropRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\Request;
use Lubart\Just\Tools\Useful;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class AbstractBlock extends Model
{   
    /**
     * Block settings form
     * 
     * @var Form $form
     */
    protected $form;
    
    protected $neededParameters = [];
    
    protected $parameters = [];
    
    protected $block_id;
    
    /**
     * Current block
     * 
     * @var Block $block
     */
    protected $block;

    protected $imageSizes = [12, 9, 8, 6, 4, 3];
    
    protected $settingsTitle = 'Model';
    
    public function __construct() {
        parent::__construct();
        
        $this->form = new Form;
    }
    
    /**
     * Block content
     * 
     * @param integer $id
     * @return mixed
     */
    public function content($id = null) {
        if(is_null($id)){
            $content = $this->orderBy('orderNo')
                    ->where('block_id', $this->block_id);
            if(!\Config::get('isAdmin')){
                $content = $content->where('isActive', 1);
            }
            
            $curCategory = $this->block()->currentCategory();
            if(!is_null($curCategory) and $curCategory->addon->block->id == $this->block_id){
                $content = $content
                        ->join($this->table."_categories", $this->table."_categories.modelItem_id", "=", $this->table.".id")
                        ->where("addonItem_id", $this->block()->currentCategory()->id);
            }
            
            $with = [];
            foreach($this->addons() as $addon){
                $with[] = $addon->name;
            }
            
            return $content->with($with)->get();
        }
        else{
            return $this->find($id);
        }
    }
    
    /**
     * Return block preview in the settings
     */
    public function preview(){
        return '';
    }
    
    /**
     * Return block settings form for admin panel
     * 
     * @return SettingsForm
     */
    abstract public function form();
    
    protected function setValuesFromRequest($request) {
        foreach($request->attributes() as $attr=>$val){
            if(in_array($attr, $this->fillable)){
                $this->{$attr} = $val;
            }
        }
    }
    
    public function settingsTitle() {
        return $this->settingsTitle;
    }
    
    public function neededParameters() {
        return $this->neededParameters;
    }
    
    /**
     * Change an order
     * 
     * @param string $dir direction, available values are up, down
     * @param array $where where statement
     */
    public function move($dir, $where = []) {
        if(empty($where)){
            $where = ['block_id' => $this->block_id];
        }
        
        Useful::moveModel($this, $dir, $where);
    }
    
    /**
     * Change an order and put model to the specific position
     * 
     * @param integer $newPosition new element position
     * @param array $where where statement
     */
    public function moveTo($newPosition, $where = []) {
        if(empty($where)){
            $where = ['block_id' => $this->block_id];
        }
        
        Useful::moveModelTo($this, $newPosition, $where);
    }
    
    /**
     * Treat request with image crop data
     * 
     * @param CropRequest $request
     * @return Image
     */
    public function handleCrop(CropRequest $request) {
        $image = Image::make($this->image($request->get('img')));
        
        $image->save($this->image($request->get('img')."_original"));
        
        $image->crop($request->get('w'), $request->get('h'), $request->get('x'), $request->get('y'));
        $image->save($this->image($request->get('img')));
        
        $this->multiplicateImage($request->get('img'));
        
        return $image;
    }
    
    /**
     * Make a copies of the image with different sizes
     * 
     * @return type
     */
    public function multiplicateImage($imageCode) {
        $image = Image::make($this->image($imageCode));
        
        if(!empty($this->imageSizes)){
            foreach($this->imageSizes as $size){
                $image->resize($this->block()->layout()->width*$size/12, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save(public_path('storage/'.$this->table.'/'. $imageCode."_".$size.".png"));
            }
        }
        else{
            $image->save(public_path('storage/'.$this->table.'/'. $imageCode.".png"));
        }
        
        return;
    }
    
    /**
     * Set model parameters
     * 
     * @param array $parameters
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }
    
    /**
     * Return all block parameters
     * 
     * @return mixed
     */
    public function parameters() {
        return json_decode($this->block()->parameters);
    }
    
    /**
     * Get specific block parameter by name
     * 
     * @param string $param parameter name
     * @return mixed
     */
    public function parameter($param) {
        $params = json_decode($this->block()->parameters);
        
        return isset($params->{$param})?$params->{$param}:null;
    }
    
    /**
     * Presetup current block
     * 
     * @return void
     */
    public function setup() {
        return;
    }
    
    public function setBlock($block_id) {
        $this->block_id = $block_id;
        $this->setAttribute('block_id', $block_id);
    }
    
    /**
     * Return current block
     * 
     * @return Block
     */
    protected function block() {
        if(is_null($this->block)){
            $this->setBlock($this->getAttribute('block_id'));
            $this->block = Block::find($this->block_id);
        }
        
        return $this->block;
    }
    
    /**
     * Return setup form for the current block
     * 
     * @param Block $block
     * @return Form
     */
    public function setupForm(Block $block) {
        $parameters = json_decode($block->parameters);
        
        $form = new Form('/admin/settings/setup');
        
        $form->setType('setup');
        
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$block->id]));
        foreach($this->neededParameters() as $param=>$label){
            $form->add(FormElement::text(['name'=>$param, 'label'=>$label, 'value'=>@$parameters->{$param}]));
        }
        
        foreach($this->customAttributes() as $attr){
            $form->add(FormElement::text(['name'=>$attr->name, 'label'=>$label, 'value'=>isset($parameters->{$attr->name})?$parameters->{$attr->name}:$attr->defaultValue]));
        }
        
        $this->addSetupFormElements($form);
        
        $form->add(FormElement::select([
            'name'=>'settingsScale',
            'label'=>'Settings View Scale',
            'value'=>isset($parameters->settingsScale)?$parameters->settingsScale:100,
            'options'=>[
                '33'=>'33% - 12 items in row',
                '40'=>'40% - 10 items in row',
                '50'=>'50% - 8 items in row',
                '67'=>'67% - 6 items in row',
                '80'=>'80% - 5 items in row',
                '100'=>'100% - 4 items in row',
                '133'=>'133% - 3 items in row',
                '200'=>'200% - 2 items in row',
                '400'=>'400% - 1 item in row']]));
     
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function addSetupFormElements(Form &$form) {
        return $form;
    }
    
    /**
     * Return image path related to the model
     * 
     * @param string $imageCode image code
     * @param int $width specify image size 
     * @return string full path to the image
     */
    public function image($imageCode, $width=null){
        if(!in_array($width, $this->imageSizes)){
            return public_path('storage/'.$this->table.'/'.$imageCode.".png");
        }
        else{
            return public_path('storage/'.$this->table.'/'.$imageCode."_".$width.".png");
        }
    }
    
    /**
     * Remove images related to the specific code
     * 
     * @param string $imageCode
     */
    public function deleteImage($imageCode){
        foreach(glob(public_path('storage/'.$this->table.'/'.$imageCode."*")) as $image){
            unlink($image);
        }
    }

        /**
     * Return current layout
     * 
     * @return \Lubart\Just\Structure\Layout;
     */
    public function layout() {
        return $this->block()->layout();
    }
    
    /**
     * Include new addon elements related to the addon
     */
    public function includeAddons() {
        foreach ($this->block()->addons() as $addon) {
            $addon->updateForm($this->form, $this->addonValues($addon->id));
        }
    }
    
    /**
     * Include new addon elements related to the addon
     * 
     * @param Request $request
     * @param mixed $item Model item
     */
    public function handleAddons(Request $request, $item) {
        foreach ($this->block()->addons() as $addon) {
            $addon->handleForm($request, $item);
        }
    }
    
    public function addonValues($addonId) {
        $addon = Addon::find($addonId)->addon();
        $addonClass = new $addon;

        return $this->belongsToMany($addon, $this->getTable().'_'.$addonClass->getTable(), 'modelItem_id', 'addonItem_id')->get();
    }
    
    /**
     * Get related addons
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addons() {
        return $this->block()->addons();
    }
    
    public function customAttributes() {
        return DB::table('blockAttributes')
                ->where('block', $this->block()->name)->get();
    }
    
    public function categories() {
        return $this->belongsToMany(Addon\Categories::class, $this->getTable().'_categories', 'modelItem_id', 'addonItem_id');
    }
    
    public function strings() {
        return $this->belongsToMany(Addon\Strings::class, $this->getTable().'_strings', 'modelItem_id', 'addonItem_id');
    }
    
    public function images() {
        return $this->belongsToMany(Addon\Images::class, $this->getTable().'_images', 'modelItem_id', 'addonItem_id');
    }
    
    public function paragraphs() {
        return $this->belongsToMany(Addon\Paragraphs::class, $this->getTable().'_paragraphs', 'modelItem_id', 'addonItem_id');
    }
    
    public function relationsForm($relBlock) {
        $form = new Form('/admin/settings/relations/create');
        
        $form->setType('relations');
        
        $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block_id]));
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->id]));
        $form->add(FormElement::select(['name'=>'relatedBlockName', 'label'=>'Related Block Type', 'value'=>(!is_null($relBlock) ? $relBlock->name : ""), 'options'=>$this->block()->allBlocksSelect()]));
        if(!is_null($relBlock)){
            $form->getElement("relatedBlockName")->setParameters("disabled", "disabled");
        }
        $form->add(FormElement::text(['name'=>'title', 'label'=>'Title', 'value'=> (!is_null($relBlock) ? $relBlock->title : "")]));
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>'Description', 'value'=>(!is_null($relBlock) ? $relBlock->description : "")]));
        
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function relatedBlocks() {
        if(Schema::hasTable($this->getTable().'_blocks')){
            return $this->belongsToMany(Block::class, $this->getTable().'_blocks', 'modelItem_id', 'block_id');
        }
        else{
            // return null
            return $this->belongsTo(Block::class);
        }
    }
    
    /**
     * Return specyfic related block
     * 
     * @param string $name type of related block
     * @param string $title title of related block
     * @param int $id id of related block
     * @return Block|null
     */
    public function relatedBlock($name, $title = null, $id = null) {
        if(Schema::hasTable($this->getTable().'_blocks')){
            $relBlock = $this->belongsToMany(Block::class, $this->getTable().'_blocks', 'modelItem_id', 'block_id')
                    ->where('name', $name);
            if(!is_null($title)){
                $relBlock->where('title', $title);
            }
            if(!is_null($id)){
                $relBlock->where('blocks.id', $id);
            }
            $relBlock = $relBlock->first();
            
            if(empty($relBlock)){
                // return null
                return $relBlock;
            }
            
            return $relBlock->specify();
        }
        else{
            // return null
            return $this->belongsTo(Block::class);
        }
    }
    
    /**
     * Return first item from content
     * 
     * @return mixed
     */
    public function firstItem(){
        return $this->content()->first();
    }
}
