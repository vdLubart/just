<?php

namespace Just\Models\Blocks;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Just\Contracts\AddOnItem;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\AddOn;
use Just\Contracts\BlockItem;
use Just\Models\Layout;
use stdClass;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Just\Requests\CropRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Models\Block;
use Just\Tools\Useful;
use Illuminate\Support\Facades\Schema;

/**
 * Class AbstractItem
 * @package Just\Models\Blocks
 *
 * @property Block $block
 * @property Collection|AddOn[] $addons
 */
abstract class AbstractItem extends Model implements BlockItem
{
    /**
     * Block item settings form
     *
     * @var ?Form $form
     */
    protected ?Form $form;

    /**
     * Parameters which should be set before use block
     *
     * @var array $neededParameters
     */
    protected array $neededParameters = [];

    protected stdClass $parameters;

    protected array $imageSizes = [12, 9, 8, 6, 4, 3];

    /**
     * Validation rules for public request inside a block.
     * Can be used by blocks with public forms like Feedback, Contacts, Events etc.
     *
     * @var array
     */
    protected array $publicRequestValidationRules = [];

    /**
     * Validation messages for public request inside a block.
     * Can be used by blocks with public forms like Feedback, Contacts, Events etc.
     *
     * @var array
     */
    protected array $publicRequestValidationMessages = [];

    public function __construct() {
        parent::__construct();

        if(Auth::id()){
            $this->form = new Form('/settings/block/item/save');
        }
        else{
            $this->form = null;
        }
    }

    /**
     * Block item content
     *
     * @return mixed
     */
    public function content() {
        $content = $this->where('block_id', $this->block->id);
        if(!Config::get('isAdmin')){
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
            $with[] = Str::plural($addon->type);
        }

        $collection = $content->with($with)->get();

        foreach($collection as $item){
            $item->attachAddons();
        }

        return $collection;
    }

    protected function attachAddons() {
        foreach($this->addons as $addon){
            $addonItem = $this->addOnItem($addon->type, $addon->id);
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
     * @param $content
     * @param string $column
     * @return mixed
     */
    protected function orderContent(&$content, string $column = 'orderNo'){
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
     * Initialize and describe block item settings form
     *
     * @return Form
     */
    abstract public function itemForm(): Form;

    /**
     * Return block item settings form
     *
     * @return Form
     */
    public function form(): Form {
        return $this->form;
    }

    /**
     * Return block item settings title in the current locale
     *
     * @return array|Application|Translator|string|null
     */
    public function settingsTitle() {
        return __($this->block->type . '.title');
    }

    /**
     * Handle request to create/update model item
     *
     * @param ValidateRequest $request
     * @return mixed
     */
    abstract public function handleItemForm(ValidateRequest $request);

    /**
     * Return parameters should be set before use block
     *
     * @return array
     */
    public function neededParameters(): array {
        return $this->neededParameters;
    }

    /**
     * Change an order
     *
     * @param string $dir direction, available values are up, down
     * @param array $where where statement
     */
    public function move(string $dir, array $where = []) {
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
    public function moveTo(int $newPosition, array $where = []) {
        if(empty($where)){
            $where = ['block_id' => $this->block->id];
        }

        Useful::moveModelTo($this, $newPosition, $where);
    }

    /**
     * Handle the cropping of the image related to the item
     *
     * @param CropRequest $request
     * @return \Intervention\Image\Image
     */
    public function handleCrop(CropRequest $request): \Intervention\Image\Image {
        $filePath = $this->imagePath("original");

        $image = Image::make($filePath);

        $image->save(public_path('/storage/'.$this->table.'/'.$this->image.'_original.png'));

        $image->crop($request->w, $request->h, $request->x, $request->y);
        $image->save($this->imagePath());

        $this->multiplicateImage($this->image);

        $this->renameImage();

        return $image;
    }

    /**
     * Set the new name for the image and rename all related images on the disk
     */
    public function renameImage(): void {
        $oldName = $this->image;

        $this->image = uniqid();
        $this->save();

        foreach(glob(public_path('/storage/'.$this->table.'/'.$oldName.'*.png')) as $filename){
            rename($filename, str_replace($oldName, $this->image, $filename));
        }
    }

    /**
     * Make copies of the image with different sizes
     *
     * @return void
     */
    public function multiplicateImage($imageCode) {
        $imageSizes = $this->imageSizes;
        if($this->parameter('customSizes')){
            $imageSizes = $this->parameter('photoSizes');
        }

        if(!empty($imageSizes)){
            foreach($imageSizes as $size){
                $image = Image::make($this->imagePathByCode($imageCode));
                $image->resize($this->block->layout()->width*$size/12, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save(public_path('/storage/'.$this->table.'/'.$imageCode.'_'.$size.'.png'));
                $image->destroy();
            }
        }
    }

    /**
     * Set model parameters
     *
     * @param stdClass $parameters
     */
    public function setParameters(stdClass $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * Get specific block parameter by name
     *
     * @param string $param parameter name
     * @param boolean $decode apply json_decode if needed
     * @return mixed
     */
    public function parameter(string $param, bool $decode = false) {
        $param = @$this->block->parameters->{$param};

        if($decode){
            return json_decode($param);
        }

        return $param;
    }

    /**
     * Pre setup current block
     *
     * @return void
     */
    public function setup() {}

    public function setBlock($block_id) {
        $this->setAttribute('block_id', $block_id);
    }

    /**
     * Return current block
     *
     * @return BelongsTo
     */
    protected function block(): BelongsTo {
        return $this->belongsTo(Block::class);
    }

    /**
     * Return customization form for the current block
     *
     * @return Form
     * @throws Exception
     */
    public function customizationForm(): Form {
        $form = new Form('/settings/block/customize');

        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->block->id]));

        $this->addCustomizationFormElements($form);

        $this->addOrderDirection($form);

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }

    public function addCustomizationFormElements(Form &$form): Form {
        return $form;
    }

    /**
     * Return image path of the current item
     *
     * @param int|string|null $width the image size or "original" string to receive uploaded image
     * @return string|null full path to the image
     */
    public function imagePath($width = null): ?string{
        if(empty(@$this->image)){
            return null;
        }

        return public_path($this->imageSource($width, $this->image));
    }

    /**
     * Return image path by unique code
     *
     * @param string $imageCode image code
     * @param int|string|null $width smallest image size
     * @return string
     */
    public function imagePathByCode(string $imageCode, $width=null): string {
        return public_path($this->imageSource($width, $imageCode));
    }

    /**
     * Return image URI related to the model
     *
     * @param int|string|null $width smallest image size or original image
     * @param string|null $imageCode image code
     * @return string image uri
     */
    public function imageSource($width = null, ?string $imageCode = null): string {
        if(empty($imageCode)){
            $imageCode = $this->image;
        }

        if(!is_null($width)){
            if($source = $this->findImage($imageCode, $width)) {
                return $source;
            }
            // if the image with requested size does not exist (due to block customization settings, for example)
            // next bigger image size is returned
            elseif(is_int($width)){
                foreach (array_sort($this->imageSizes) as $size){
                    if($size > $width and $source = $this->findImage($imageCode, $size)) {
                        return $source;
                    }
                }
            }
        }

        return '/storage/'.$this->table.'/'.$imageCode.".png";
    }

    private function findImage($imageCode, $width) {
        if(file_exists(public_path('/storage/'.$this->table.'/'.$imageCode."_".$width.".png"))) {
            return '/storage/' . $this->table . '/' . $imageCode . "_" . $width . ".png";
        }

        return false;
    }

    /**
     * Remove images related to the specific code
     *
     * @param string $imageCode
     */
    public function deleteImage(string $imageCode){
        foreach(glob(public_path('storage/'.$this->table.'/'.$imageCode."*")) as $image){
            unlink($image);
        }
    }

    /**
     * Include new addon elements related to the addon
     * @throws Exception
     */
    public function includeAddons() {
        foreach ($this->block->addons->sortBy('orderNo') as $addon) {
            $addon->updateForm($this);
        }
    }

    /**
     * Include new addon elements related to the addon
     *
     * @param ValidateRequest $request
     * @param mixed $item Model item
     * @throws Exception
     */
    public function handleAddons(ValidateRequest $request, $item) {
        foreach ($this->block->addons as $addon) {
            $addon->handleForm($request, $item);
        }
    }

    /**
     * Get related addons
     *
     * @return HasMany
     */
    public function addons(): HasMany {
        return $this->block->addons();
    }

    /**
     * Get related addon item
     *
     * @param string $name addon name
     * @return AddOnItem
     */
    public function addon(string $name): AddOnItem {
        $addon = Addon::where('name', $name)->first();

        return $this->belongsToMany('Just\\Models\\Blocks\\AddOns\\'.ucfirst($addon->type), $this->getTable().'_'.Str::plural($addon->type), 'modelItem_id', 'addonItem_id')->first();
    }


    public function categories(): BelongsToMany {
        return $this->belongsToMany(AddOns\Category::class, $this->getTable().'_categories', 'modelItem_id', 'addonItem_id');
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(AddOns\Tag::class, $this->getTable().'_tags', 'modelItem_id', 'addonItem_id');
    }

    public function phrases(): BelongsToMany {
        return $this->belongsToMany(AddOns\Phrase::class, $this->getTable().'_phrases', 'modelItem_id', 'addonItem_id');
    }

    public function images(): BelongsToMany {
        return $this->belongsToMany(AddOns\Image::class, $this->getTable().'_images', 'modelItem_id', 'addonItem_id');
    }

    public function paragraphs(): BelongsToMany {
        return $this->belongsToMany(AddOns\Paragraph::class, $this->getTable().'_paragraphs', 'modelItem_id', 'addonItem_id');
    }

    protected function addOnItem(string $name, int $id) {
        return $this->{Str::plural($name)}()->where('add_on_id', $id)->first();
    }

    /**
     * @throws Exception
     */
    public function relationsForm($relBlock): Form {
        $form = new Form('/admin/settings/relations/create');

        // TODO: Check this method
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
     * @param string|null $name name of related block
     * @param int|null $id id of related block
     * @return Block|null
     */
    public function relatedBlock(string $type, ?string $name = null, ?int $id = null): ?Block {
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

    protected function addIgnoreCaptionSetupGroup(&$form){
        $photoFieldsGroup = new FormGroup('fieldsGroup', __('block.customizations.fieldsGroup.title'));
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreCaption', 'label' => __('block.customizations.fieldsGroup.ignoreCaption'), 'check'=>$this->parameter('ignoreCaption', true)]));
        $photoFieldsGroup->add(FormElement::checkbox(['name'=>'ignoreDescription', 'label' => __('block.customizations.fieldsGroup.ignoreDescription'), 'check'=>$this->parameter('ignoreDescription', true)]));
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
    public function haveSlug(): bool {
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

    public function identifyItemForm() {
        $this->form->add(FormElement::hidden(['name'=>'id', 'value'=>@$this->id]));
        $this->form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block->id]));
    }

    /**
     * Return the list of the needed JavaScripts for the view templates
     *
     * @return array
     */
    public function neededJavaScripts(): array {
        return [];
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

    protected function createUploadDirectory() {
        if(!file_exists(public_path('storage/'. $this->table))){
            mkdir(public_path('storage/'. $this->table), 0775);
        }
    }
}
