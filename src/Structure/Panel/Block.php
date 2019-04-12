<?php

namespace Lubart\Just\Structure\Panel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel;
use Lubart\Just\Structure\Page;
use Lubart\Just\Models\Route;
use Lubart\Just\Structure\Layout;
use Lubart\Just\Structure\Panel\Block\Addon;
use Lubart\Form\Form;
use Illuminate\Support\Facades\DB;
use Lubart\Just\Tools\Useful;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class Block extends Model
{   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'panelLoation', 'page_id', 'title', 'description', 'width', 'cssClass', 'orderNo', 'isActive', 'parameters', 'parent'
    ];
    
    protected $table = 'blocks';
    
    protected $model;
    
    /**
     * Access parent block from the related block
     * 
     * @var Block $parentBlock
     */
    protected $parentBlock = null;
    
    protected $currentCategory = null;
    
    /**
     * Specify model and addons for current block
     * 
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function specify($id = null) {
        $name = "\\Lubart\\Just\\Structure\\Panel\\Block\\". ucfirst($this->type);
        
        // looking for a custom block
        if(!class_exists($name)){
            $name = "\\App\\Just\\Panel\\Block\\". ucfirst($this->type);
            if(!class_exists($name)){
                throw new \Exception("Block class \"".ucfirst($this->type)."\" not found");
            }
        }
        
        if($id > 0){
            $this->model = $name::findOrNew($id);
        }
        else{
            $this->model = new $name;
        }
        $this->model->setParameters($this->parameters);
        $this->model->setBlock($this->id);
        $this->model->setup();
        
        foreach($this->addons as $addon){
            $this->{$addon->name} = Addon::find($addon->id);
        }
        
        return $this;
    }
    
    /**
     * Unsettle model data
     * 
     * @return $this
     */
    public function unsettle(){
        $this->model = null;
        
        foreach($this->addons as $addon){
            unset($this->{$addon->name});
        }
        
        return $this;
    }
    
    public function form() {
        $form = $this->model->form();
        if(!is_null($form) and is_null($form->getElement('block_id'))){
            $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->id]));
            $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->model->id]));
        }
        
        return $form;
    }
    
    public function relationsForm($relBlock) {
        $form = $this->model->relationsForm($relBlock);
        
        return $form;
    }
    
    public function panelForm() {
        $form = new Form('/admin/settings/panel/setup');
        
        if(!is_null($this->id)){
            $form->open();
        }
        
        if(empty($form->getElements())){
            $form->add(FormElement::hidden(['name'=>'panel_id', 'value'=>$this->panel_id]));
            $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>@$this->id]));
            $form->add(FormElement::hidden(['name'=>'page_id', 'value'=>$this->page_id]));
            $form->add(FormElement::select(['name'=>'type', 'label'=>'Type', 'value'=>@$this->type, 'options'=>$this->allBlocksSelect()]));
            if(!is_null($this->id)){
                $form->getElement("type")->setParameters("disabled", "disabled");
            }
            $form->add(FormElement::text(['name'=>'name', 'label'=>'Name', 'value'=>@$this->name]));
            $form->add(FormElement::text(['name'=>'title', 'label'=>'Title', 'value'=>@$this->title]));
            $form->add(FormElement::textarea(['name'=>'description', 'label'=>'Description', 'value'=>@$this->description, "class"=>"ckeditor"]));
            $form->applyJS("$(document).ready(function(){CKEDITOR.replace('description') });");
            if($this->layout()->type == 'float'){
                $form->add(FormElement::select(['name'=>'width', 'label'=>'Width', 'value'=>$this->width ?? 12, 'options'=>[3=>"25%", 4=>"33%", 6=>"50%", 8=>"67%", 9=>"75%", 12=>"100%"]]));
            }
            if(\Auth::user()->role == "master"){
                $form->add(FormElement::text(['name'=>'layoutClass', 'label'=>'Layout Class', 'value'=>$this->layoutClass ?? 'primary']));
                $form->add(FormElement::text(['name'=>'cssClass', 'label'=>'Additional CSS Class', 'value'=>@$this->cssClass]));
            }
            $form->add(FormElement::submit(['value'=>'Save']));
        }
        
        return $form;
    }
    
    public function blockForm() {
        $form = $this->panelForm();
        $form->setAction('/admin/settings/block/setup');
        
        return $form;
    }
    
    public function allBlocksSelect() {
        $blocks = DB::table('blockList')
                ->select('block', 'title')
                ->get()->toArray();
        
        $select = [];
        foreach($blocks as $block){
            $select[$block->block] = $block->title;
        }
        
        return $select;
    }
    
    public function content() {
        return $this->model()->content();
    }
    
    /**
     * Return first item from content
     * 
     * @return mixed
     */
    public function firstItem(){
        $content = $this->content();
        
        if($content instanceof \Illuminate\Database\Eloquent\Collection){
            return $content->first();
        }
        
        switch (true){
            case ($this->model() instanceof Block\Feedback) :
                return $content->messages->first();
        }
        
        return $content;
    }
    
    /**
     * Return parent block
     * @param boolean $highestLevel is parent of top level should be returned
     * @return  Block return parent block or itself if block does not have parent.
     */
    public function parentBlock($highestLevel = false){
        if(!is_null($this->parent)){
            $parentBlock = Block::find($this->parent);
            
            if(!$highestLevel){
                return $parentBlock;
            }
            else{
                return $parentBlock->parentBlock(true);
            }
        }
        else{
            return $this;
        }
    }
    
    /**
     * Return item to which current block is connected
     * 
     * @return mixed
     */
    public function parentItem(){
        $parentBlock = $this->parentBlock()->specify()->model();
        
        $itemId = DB::table($parentBlock->getTable()."_blocks")->where("block_id", $this->id)->get(["modelItem_id"]);
        
        if(!$itemId->isEmpty()){
            return $parentBlock::find($itemId->first()->modelItem_id);
        }
        
        return null;
    }
    
    public function handleForm(Request $request, $isPublic = false) {
        $method = !$isPublic ? 'handleForm' : 'handlePublicForm';
        $reflection = new \ReflectionMethod($this->model, $method);
        $validatorClass = $reflection->getParameters()[0]->getClass()->name;
        
        $validatedRequest = new $validatorClass;
        if($validatorClass != 'Illuminate\Http\Request' and $validatedRequest->authorize()){
            $addonValidators = [];
            foreach ($this->addons as $addon){
                $addonValidators += $addon->validationRules();
            }
            
            $validator = \Validator::make($request->all(),
                    $validatedRequest->rules()+$addonValidators+['block_id' => "required|integer|min:1"],
                    $validatedRequest->messages());
            
            if($validator->fails()){
                return $validator;
            }
            
            foreach($request->all() as $name=>$param){
                if($param instanceof UploadedFile){
                    $validatedRequest->files->set($name, $param);
                }
                else{
                    $validatedRequest->request->set($name, $param);
                }
            }
        }
        elseif($validatorClass == 'Illuminate\Http\Request'){
            $validatedRequest = $request;
        }
        
        return $this->model->{$method}($validatedRequest);
    }
    
    public function handlePanelForm(Request $request) {
        if(is_null($this->id)){
            $this->type = $request->type;
        }
        $panel = Panel::find($request->panel_id);
        $this->panelLocation = $panel->location;
        $this->page_id = $request->page_id;
        $this->name = $request->name;
        $this->title = $request->title ?? "";
        $this->description = $request->blockDescription ?? "";
        $this->width = $request->width ?? ( $this->width ?? 12 );
        $this->layoutClass = (\Auth::user()->role == "master" ? $request->layoutClass : $this->layoutClass) ?? 'primary';
        $this->cssClass = (\Auth::user()->role == "master" ?  $request->cssClass : $this->cssClass) ?? '';
        $this->orderNo = $this->orderNo?$this->orderNo : Useful::getMaxNo($this->table, ['panelLocation' => $panel->location, "page_id"=>$request->page_id]);
        
        $this->save();
        
        return $this;
    }
    
    public function handleCrop(Request $request) {
        $reflection = new \ReflectionMethod($this->model, 'handleCrop');
        $validatorClass = $reflection->getParameters()[0]->getClass()->name;
        
        $validatedRequest = new $validatorClass;
        if($validatedRequest->authorize()){
            $validData = $request->validate($validatedRequest->rules()+['block_id' => "required|integer|min:1"], $validatedRequest->messages());
            foreach($validData as $name=>$param){
                $validatedRequest->request->set($name, $param);
            }
        }
        
        return $this->model->handleCrop($validatedRequest);
    }
    
    public function deleteModel() {
        //Delete whole block
        if(is_null($this->model->id)){
            $this->deleteImage($this->model);
            $this->delete();
        }
        // Delete model item in the block
        else{
            $this->deleteImage($this->model);
            $this->model->delete();
        }
    }
    
    protected function deleteImage($model) {
        if(isset($model->getAttributes()['image'])){
            foreach (glob(public_path('storage/'.$model->getTable().'/*').$model->image."*") as $img){
                unlink($img);
            }
        }
    }
    
    public static function findModel($blockId, $id) {
        $block = self::find($blockId);
        
        if(!$block){
            throw new \Exception("Block not found");
        }
        
        $block->specify($id);
        
        return $block;
    }
    
    public static function findByName($name){
        $block = self::where('name', $name)->get();
        
        if($block->isEmpty()){
            throw new \Exception("Block not found");
        }
        
        return $block->first(); 
    }
    
    public function model() {
        return $this->model;
    }
    
    public function models() {
        $name = "\\Lubart\\Just\\Structure\\Panel\\Block\\". ucfirst($this->type);
        return $this->hasMany($name);
    }
    
    public function move($dir) {
        // Move whole block
        if(is_null($this->model->id)){
            $where = [
                    'panelLocation' => $this->panelLocation,
                    'page_id' => $this->page_id
                ];
            
            return Useful::moveModel($this->unsettle(), $dir, $where);
        }
        // Move model item in the block
        else{
            return $this->model->move($dir);
        }
    }
    
    public function moveTo($newPosition) {
        // Move whole block
        if(is_null($this->model->id)){
            $where = [
                    'panelLocation' => $this->panelLocation,
                    'page_id' => $this->page_id
                ];
            
            return Useful::moveModelTo($this, $newPosition, $where);
        }
        // Move model item in the block
        else{
            $this->model->moveTo($newPosition);
        }
    }
    
    public function visabiliy($visabiliy) {
        if(is_null($this->model->id)){
            $model = $this;
        }
        else{
            $model = $this->model;
        }

        $model->isActive = $visabiliy;
        $model->save();

        return $model;
    }
    
    public function isSetted() {
        $parameters = json_decode($this->parameters);
        
        $isSetted = true;
        foreach($this->model->neededParameters() as $param=>$label){
            if(!isset($parameters->{$param})){
                $isSetted = false;
                break;
            }
        }
        
        return $isSetted;
    }
    
    public function setupForm() {
        return $this->model->setupForm($this);
    }
    
    public function parameters() {
        return json_decode($this->parameters);
    }
    
    public function parameter($param) {
        return @json_decode($this->parameters)->{$param};
    }
    
    /**
     * Return route where current block is located
     * 
     * @return Route
     */
    public function route() {
        return $this->belongsTo(Panel::class, 'panel_id')->first()
                ->belongsTo(Page::class, 'page_id')->first()
                ->belongsTo(Route::class, 'route', 'route');
    }
    
    /**
     * Return current layout
     * 
     * @return Layout
     */
    public function layout() {
        $url = trim(str_replace(request()->root()."/admin", '', request()->server('HTTP_REFERER')), '/');
        $route = app('router')->getRoutes()->match(app('request')->create($url));
        
        return @Page::where('route', trim($route->uri, '/'))->first()->layout;
    }
    
    /**
     * Return addons related to the current model
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addons() {
        return $this->hasMany(Addon::class);
    }
    
    public function addon($addonId) {
        return Addon::find($addonId)->addon();
    }
    
    public static function getAddonByName($name) {
        return Addon::where('name', $name)->first();
    }
    
    /**
     * Return panel where block is located
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function panel() {
        return $this->belongsTo(Panel::class);
    }
    
    /**
     * Specify block panel
     * 
     * @param Panel $panel
     * @return Block
     */
    public function setPanel(Panel $panel) {
        $this->panel_id = $panel->id;
        $this->page_id = is_null($panel->page())?null:$panel->page()->id;
        
        return $this;
    }
    
    public function categories() {
        return $this->addons()->where('type', 'categories');
    }
    
    public function strings() {
        return $this->addons()->where('type', 'strings');
    }
    
    public function images() {
        return $this->addons()->where('type', 'images');
    }
    
    public function paragraphs() {
        return $this->addons()->where('type', 'paragraphs');
    }
    
    public function currentCategory() {
        if(is_null($this->currentCategory)){
            $this->currentCategory = Addon\Categories::where('value', request('category'))->first();
        }
        
        return $this->currentCategory;
    }
    
    /**
     * Page belongs to the current block
     * 
     * @return type
     */
    public function page() {
        return $this->belongsTo(Page::class);
    }
    
    /**
     * Create migration for relationship between model and block
     * 
     * @param type $modelTable
     */
    public static function createPivotTable($modelTable) {
        if(!Schema::hasTable($modelTable."_blocks")){
            Artisan::call("make:relatedBlockMigration", ["name" => "create_".$modelTable."_blocks_table"]);
            
            Artisan::call("migrate", ["--step" => true]);
        }
    }
    
    public function details(){
        return $this->join('blockList', $this->table.'.type', '=', 'blockList.block')
                ->where($this->table.'.id', $this->id)
                ->first();
    }
}