<?php

namespace Just\Models\Blocks;

use Illuminate\Database\Eloquent\Model;
use Just\Models\Blocks\Contracts\BlockItem;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Just\Requests\CropRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Models\Block;
use Illuminate\Http\Request;
use Just\Tools\Useful;
use Illuminate\Support\Facades\Schema;
use Just\Models\Blocks\Contracts\ValidateRequest;

abstract class AbstractBlock extends Model implements BlockItem
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
            $this->form = new Form('/settings/block/item/setup');
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
                    $this->{$addon->name} = [$addonItem->value => $addonItem->title];
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
        return $content->orderBy($column, ( $this->parameter('orderDirection') ?: 'asc'));
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
    abstract public function settingsForm(): Form;

    /**
     * Return block settings title in the current locale
     *
     * @return mixed
     */
    public function settingsTitle() {
        return __($this->block->type . '.title');
    }

    /**
     * Handle request to create/update model item
     *
     * @param Contracts\ValidateRequest $request
     * @return mixed
     */
    abstract public function handleSettingsForm(ValidateRequest $request);

    /**
     * Return parameters should be set before use block
     *
     * @return array
     */
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
     * Get specific block parameter by name
     * 
     * @param string $param parameter name
     * @return mixed
     */
    public function parameter($param) {
        return @$this->block->parameters->{$param};
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
     * @return Form
     * @throws
     */
    public function customizationForm() {
        $form = new Form('/settings/block/customize');

        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->block->id]));
        
        $this->addCustomizationFormElements($form);

        $this->addOrderDirection($form);

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }
    
    public function addCustomizationFormElements(Form &$form) {
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
        return $this->imageSrc($imageCode, $width);
    }

    /**
     * Return image URI related to the model
     *
     * @param string $imageCode image code
     * @param int $width specify image size
     * @return string image uri
     */
    public function imageSrc($imageCode, $width=null) {
        if(!in_array($width, $this->imageSizes)){
            return '/storage/'.$this->table.'/'.$imageCode.".png";
        }
        else{
            return '/storage/'.$this->table.'/'.$imageCode."_".$width.".png";
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
     * @return \Just\Structure\Layout;
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

        return $this->belongsToMany('Just\\Structure\\Panel\\Block\\Addon\\'.ucfirst($addon->type), $this->getTable().'_'.$addon->type, 'modelItem_id', 'addonItem_id')->first();
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
     * @param string $type type of related block
     * @param string $name name of related block
     * @param int $id id of related block
     * @return Block|null
     */
    public function relatedBlock($type, $name = null, $id = null) {
        $relBlock = $this->belongsToMany(Block::class, $this->getTable().'_blocks', 'modelItem_id', 'block_id')
                ->where('type', $type);
        if(!is_null($name)){
            $relBlock->where('name', $name);
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
        $photoCropGroup = new FormGroup('cropGroup', __('block.customizations.cropGroup.title'));
        $photoCropGroup->add(FormElement::checkbox(['name'=>'cropPhoto', 'label'=>__('settings.actions.crop'), 'check'=>(filter_var($this->parameter('cropPhoto'), FILTER_VALIDATE_BOOLEAN)), 'onchange'=>'CustomizeBlock.checkCropDimensionsVisibility()']));
        $photoCropGroup->add(FormElement::text(['name'=>'cropDimensions', 'label'=>__('block.customizations.cropGroup.cropDimensions'), 'value'=> $this->parameter('cropDimensions') ?? '4:3']));
        $form->addGroup($photoCropGroup);

        $form->applyJS("window.CustomizeBlock = this.getClassInstance('CustomizeBlock');");
    }
    
    protected function addIgnoretCaptionSetupGroup(&$form){
        $photoFieldsGroup = new FormGroup('fieldsGroup', __('block.customizations.fieldsGroup.title'));
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreCaption', 'label' => __('block.customizations.fieldsGroup.ignoreCaption'), 'check'=>(filter_var($this->parameter('cropPhoto'), FILTER_VALIDATE_BOOLEAN))]));
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreDescription', 'label' => __('block.customizations.fieldsGroup.ignoreDescription'), 'check'=>$this->parameter('ignoreDescription')]));
        $form->addGroup($photoFieldsGroup);
    }
    
    protected function addResizePhotoSetupGroup(&$form){
        $photoSizesGroup = new FormGroup('sizeGroup', __('block.customizations.sizeGroup.title'));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'customSizes', 'label'=> __('block.customizations.sizeGroup.customSizes'), 'check'=>$this->parameter('customSizes'), 'onchange'=>'CustomizeBlock.checkImageSizesVisibility()' ]));
        $photoSizesGroup->add(FormElement::html(['name'=>'emptyParagraph', 'value'=>'<p></p>']));
        $photoSizesGroup->add(FormElement::checkbox(['name'=>'photoSizes', 'value'=>($this->parameter('photoSizes') ?? []), 'options'=>[
            "12" => trans_choice('block.customizations.sizeGroup.size', 12, ['width'=>'100%', 'cols'=>12]),
            "9" => trans_choice('block.customizations.sizeGroup.size', 9, ['width'=>'75%', 'cols'=>9]),
            "8" => trans_choice('block.customizations.sizeGroup.size', 8, ['width'=>'67%', 'cols'=>8]),
            "6" => trans_choice('block.customizations.sizeGroup.size', 6, ['width'=>'50%', 'cols'=>6]),
            "4" => trans_choice('block.customizations.sizeGroup.size', 4, ['width'=>'33%', 'cols'=>4]),
            "3" => trans_choice('block.customizations.sizeGroup.size', 3, ['width'=>'25%', 'cols'=>3])
        ]]));
        $form->addGroup($photoSizesGroup);

        $form->applyJS("window.CustomizeBlock = this.getClassInstance('CustomizeBlock');");
    }

    protected function addItemRouteGroup(&$form){
        $itemRouteGroup = new FormGroup('itemRoute', __('block.customizations.itemRoute.title'));
        $itemRouteGroup->add(FormElement::text(['name'=>'itemRouteBase', 'label'=>__('block.customizations.itemRoute.base'), 'value'=>$this->parameter('itemRouteBase') ?? (str_singular(str_slug($this->block->type)) ?? str_singular($this->block->name) ?? str_singular($this->block->type))])
            ->obligatory()
        );
        $form->addGroup($itemRouteGroup);
    }

    protected function addOrderDirection(&$form){
        $orderDirectionGroup = new FormGroup('orderDirection', __('block.customizations.orderDirection.title'));
        $orderDirectionGroup->add(FormElement::radio(['name'=>'orderDirection', 'label'=>__('block.customizations.orderDirection.title'), 'value'=>$this->parameter('orderDirection'), 'options'=>[
            'asc' => __('block.customizations.orderDirection.asc'),
            'desc' => __('block.customizations.orderDirection.desc')
        ]])
            ->obligatory()
        );
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

    protected function markObligatoryFields(&$form){
        $validatorClass = $this->block->findValidationRequest();

        $formValidator = new $validatorClass();
        $requiredFields = [];

        foreach ($formValidator->rules() as $field=>$rule){
            if(is_string($rule)){
                $rules = explode("|", $rule);
                if(in_array('required', $rules)){
                    $requiredFields[] = $field;
                }
            }

            if(is_array($rule)){
                if(in_array('required', $rule)){
                    $requiredFields[] = $field;
                }
            }
        }

        foreach($requiredFields as $name){
            $form->element($name)->obligatory();
        }

        return $form;
    }

    public function identifySettingsForm() {
        $this->form->add(FormElement::hidden(['name'=>'id', 'value'=>@$this->id]));
        $this->form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block->id]));
    }

    public function itemImage(): ?string {
        return null;
    }

    public function itemIcon(): ?string {
        return null;
    }

    public function itemText(): ?string {
        return null;
    }

    public function itemCaption(): ?string {
        return null;
    }
}
