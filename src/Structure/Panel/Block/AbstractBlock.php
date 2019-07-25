<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
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

    /**
     * Parameters which should be set before use block
     *
     * @var array $neededParameters
     */
    protected $neededParameters = [];
    
    protected $parameters = [];
    
    protected $imageSizes = [12, 9, 8, 6, 4, 3];

    /**
     * Title for the single block item
     *
     * @var string $settingsTitle
     */
    protected $settingsTitle;

    /**
     * Validation rules for public request inside a block.
     * Can be used by blocks with public forms like Feedback, Contacts, Events etc.
     *
     * @var array
     */
    protected $publicRequestValidationRules = [];

    /**
     * Validation messages for public request inside a block.
     * Can be used by blocks with public forms like Feedback, Contacts, Events etc.
     *
     * @var array
     */
    protected $publicRequestValidationMessages = [];
    
    public function __construct() {
        parent::__construct();

        if(\Auth::id()){
            $this->form = new Form;
        }
    }
    
    /**
     * Block content
     * 
     * @return mixed
     */
    public function content() {
        $content = $this->where('block_id', $this->block->id);
        if(!\Config::get('isAdmin')){
            $content = $content->where('isActive', 1);
        }

        $curCategory = $this->block->currentCategory();
        if(!is_null($curCategory) and $curCategory->addon->block->id == $this->block->id){
            $content = $content
                    ->join($this->table."_categories", $this->table."_categories.modelItem_id", "=", $this->table.".id")
                    ->where("addonItem_id", $this->block->currentCategory()->id);
        }

        $this->limitContent($content);
        $this->orderContent($content);

        $with = [];
        foreach($this->addons as $addon){
            $with[] = $addon->type;
        }

        $collection = $content->with($with)->get();

        foreach($collection as $item){
            $item->attachAddons();
        }

        return $collection;
    }

    protected function attachAddons() {
        foreach($this->addons as $addon){
            $addonItem = $this->{$addon->type}->where('addon_id', $addon->id)->first();
            if(!empty($addonItem)){
                if($addon->type != "categories"){
                    $this->{$addon->name} = $addonItem->value;
                }
                else{
                    $this->{$addon->name} = [$addonItem->value => $addonItem->name];
                }
            }
            else{
                $this->{$addon->name} = null;
            }
        }
    }

    /**
     * Order content by specific column
     *
     * @param string $column
     * @return mixed
     */
    protected function orderContent(&$content, $column = 'orderNo'){
        return $content->orderBy($column, $this->parameter('orderDirection') ?? 'asc');
    }

    /**
     * Limit content by specific condition.
     * Condition can be added in the model class
     *
     * @param $content
     * @return mixed
     */
    protected function limitContent(&$content){
        return $content;
    }
    
    /**
     * Return block settings form for admin panel
     * 
     * @return SettingsForm
     */
    abstract public function form();
    
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
            $where = ['block_id' => $this->block->id];
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
            $where = ['block_id' => $this->block->id];
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
        if(file_exists($this->image($request->get('img')."_original"))){
            $filePath = $this->image($request->get('img')."_original");
        }
        else{
            $filePath = $this->image($request->get('img')); 
        }
        $image = Image::make($filePath);
        
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
        $imageSizes = $this->imageSizes;
        if($this->parameter('customSizes')){
            $imageSizes = $this->parameter('photoSizes');
        }
        
        if(!empty($imageSizes)){
            foreach($imageSizes as $size){
                $image = Image::make($this->image($imageCode));
                $image->resize($this->block->layout()->width*$size/12, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save(public_path('storage/'.$this->table.'/'. $imageCode."_".$size.".png"));
                $image->destroy();
            }
        }
        else{
            $image = Image::make($this->image($imageCode));
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
        return json_decode($this->block->parameters);
    }
    
    /**
     * Get specific block parameter by name
     * 
     * @param string $param parameter name
     * @return mixed
     */
    public function parameter($param) {
        return $this->block->parameter($param);
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
        $this->setAttribute('block_id', $block_id);
    }
    
    /**
     * Return current block
     * 
     * @return Block
     */
    protected function block() {
        return $this->belongsTo(Block::class);
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
        
        $this->addSetupFormElements($form);

        $settingsViewGroup = new FormGroup('settingsView', 'Settings View', ['class'=>'col-md-6']);
        $settingsViewGroup->add(FormElement::select([
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
                '400'=>'400% - 1 item in row'
            ]]));
        $form->addGroup($settingsViewGroup);

        $this->addOrderDirection($form);

        $submitGroup = new FormGroup('submitSetup', '', ['class'=>'col-md-12 clear']);
        $submitGroup->add(FormElement::submit(['value'=>'Save']));
        $form->addGroup($submitGroup);

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
        return $this->block->layout();
    }
    
    /**
     * Include new addon elements related to the addon
     */
    public function includeAddons() {
        foreach ($this->block->addons as $addon) {
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
        foreach ($this->block->addons as $addon) {
            $addon->handleForm($request, $item);
        }
    }
    
    public function addonValues($addonId) {
        $addon = Addon::find($addonId)->addon();
        $addonClass = new $addon;
        
        return $this->belongsToMany($addon, $this->getTable().'_'.$addonClass->getTable(), 'modelItem_id', 'addonItem_id')->where('addon_id', $addonId)->get();
    }
    
    /**
     * Get related addons
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addons() {
        return $this->block->addons();
    }
    
    /**
     * Get related addon item
     * 
     * @param string $name addon name
     * @return addon item
     */
    public function addon($name){
        $addon = Addon::where('name', $name)->first();

        return $this->belongsToMany('Lubart\\Just\\Structure\\Panel\\Block\\Addon\\'.ucfirst($addon->type), $this->getTable().'_'.$addon->type, 'modelItem_id', 'addonItem_id')->first();
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
        
        $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block->id]));
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->id]));
        $form->add(FormElement::select(['name'=>'relatedBlockName', 'label'=>'Related Block Type', 'value'=>(!is_null($relBlock) ? $relBlock->type : ""), 'options'=>$this->block->allBlocksSelect()]));
        $form->add(FormElement::text(['name'=>'title', 'label'=>'Title', 'value'=> (!is_null($relBlock) ? $relBlock->title : "")]));
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>'Description', 'value'=>(!is_null($relBlock) ? $relBlock->description : "")]));
        $form->applyJS("applyCKEditor('#".$this->block->type."_relationsForm #description')");
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
     * Return specific related block
     * 
     * @param string $name type of related block
     * @param string $title title of related block
     * @param int $id id of related block
     * @return Block|null
     */
    public function relatedBlock($name, $title = null, $id = null) {
        $relBlock = $this->belongsToMany(Block::class, $this->getTable().'_blocks', 'modelItem_id', 'block_id')
                ->where('type', $name);
        if(!is_null($title)){
            $relBlock->where('title', $title);
        }
        if(!is_null($id)){
            $relBlock->where('blocks.id', $id);
        }
        $relBlock = $relBlock->first();

        if(empty($relBlock)){
            return null;
        }

        return $relBlock->specify();
    }
    
    /**
     * Return first item from content
     * 
     * @return mixed
     */
    public function firstItem(){
        return $this->content()->first();
    }
    
    protected function addCropSetupGroup(&$form){
        $photoCropGroup = new FormGroup('cropGroup', 'Image cropping', ['class'=>'col-md-6']);
        $photoCropGroup->add(FormElement::checkbox(['name'=>'cropPhoto', 'label'=>'Crop photo', 'value'=>1, 'check'=>(@$this->parameter('cropPhoto')==1)]));
        $photoCropGroup->add(FormElement::text(['name'=>'cropDimentions', 'label'=>'Crop image with dimentions (W:H)', 'value'=> $this->parameter('cropDimentions') ?? '4:3']));
        $form->addGroup($photoCropGroup);
    }
    
    protected function addIgnoretCaptionSetupGroup(&$form){
        $photoFieldsGroup = new FormGroup('fieldsGroup', 'Image fields', ['class'=>'col-md-6']);
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreCaption', 'label' => 'Ignore item caption', 'value'=>1, 'check'=>$this->parameter('ignoreCaption')]));
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreDescription', 'label' => 'Ignore item description', 'value'=>1, 'check'=>$this->parameter('ignoreDescription')]));
        $form->addGroup($photoFieldsGroup);
    }
    
    protected function addResizePhotoSetupGroup(&$form){
        $photoSizesGroup = new FormGroup('sizeGroup', 'Resize images', ['class'=>'col-md-6']);
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'customSizes', 'label'=>'Choose custom size set', 'value'=>1, 'check'=>$this->parameter('customSizes') ]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 100% layout width (12 cols)', 'value'=>12, 'check'=>(in_array(12, $this->parameter('photoSizes')??[]))]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 75% layout width (9 cols)', 'value'=>9, 'check'=>(in_array(9, $this->parameter('photoSizes')??[]))]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 67% layout width (8 cols)', 'value'=>8, 'check'=>(in_array(8, $this->parameter('photoSizes')??[]))]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 50% layout width (6 cols)', 'value'=>6, 'check'=>(in_array(6, $this->parameter('photoSizes')??[]))]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 33% layout width (4 cols)', 'value'=>4, 'check'=>(in_array(4, $this->parameter('photoSizes')??[]))]));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes[]', 'label'=>'Resize to 25% layout width (3 cols)', 'value'=>3, 'check'=>(in_array(3, $this->parameter('photoSizes')??[]))]));
        $form->addGroup($photoSizesGroup);
    }

    protected function addItemRouteGroup(&$form){
        $itemRouteGroup = new FormGroup('itemRoute', 'Item route', ['class'=>'col-md-6']);
        $itemRouteGroup->add(FormElement::text(['name'=>'itemRouteBase', 'label'=>'Item route base', 'value'=>(str_singular(str_slug($this->block->type)) ?? str_singular($this->block->name) ?? str_singular($this->block->type))]));
        $form->addGroup($itemRouteGroup);
    }

    protected function addOrderDirection(&$form){
        $orderDirectionGroup = new FormGroup('orderDirection', 'Ordering Direction', ['class'=> 'col-md-6']);
        $orderDirectionGroup->add(FormElement::radio(['name'=>'orderDirection', 'label'=>'New item appears in the end', 'value'=>'asc', 'check'=>$this->parameter('orderDirection') == 'asc']));
        $orderDirectionGroup->add(FormElement::radio(['name'=>'orderDirection', 'label'=>'New item appears on the top', 'value'=>'desc', 'check'=>$this->parameter('orderDirection') == 'desc']));
        $form->addGroup($orderDirectionGroup);
    }

    /**
     * Default value for using Slug trait.
     * The value is overwritten in the trait class
     *
     * @return bool
     */
    public function haveSlug(){
        return false;
    }
}
