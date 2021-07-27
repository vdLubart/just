<?php

namespace Just\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Just\Models\Blocks\AbstractItem;
use Just\Models\Blocks\Contracts\BlockItem;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Models\System\BlockList;
use ReflectionException;
use ReflectionMethod;
use Spatie\Translatable\HasTranslations;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lubart\Form\FormElement;
use Lubart\Form\Form;
use Illuminate\Support\Facades\DB;
use Just\Tools\Useful;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Lubart\Form\FormGroup;
use Just\Models\Blocks\AddOns\Categories;

/**
 * Class Block
 *
 * @package Just\Models
 * @property AddOn[] $addons
 * @mixin IdeHelperBlock
 */
class Block extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'panelLocation', 'page_id', 'title', 'description', 'width', 'layoutClass', 'cssClass', 'orderNo', 'isActive', 'parameters', 'parent'
    ];

    protected $casts = [
        'parameters' => 'object',
        'title' => 'object',
        'description' => 'object'
    ];

    public array $translatable = ['title', 'description'];

    protected $table = 'blocks';

    /**
     * Block item
     *
     * @var ?AbstractItem $item
     */
    protected ?AbstractItem $item = null;

    /**
     * Access parent block from the related block
     *
     * @var ?Block $parentBlock
     */
    protected ?Block $parentBlock = null;

    protected $currentCategory = null;

    /**
     * Specify model and addons for current block
     *
     * @param int|string|null $id item id or slug
     * @return Block
     * @throws Exception
     */
    public function specify($id = null): Block {
        $name = $this->itemClassName();

        // make sure if string $id parameter is a number
        if((int)$id > 0 and (string)(int)$id == $id){
            $this->item = $name::findOrNew($id);
        }
        else{
            $this->item = new $name;

            if($this->item->haveSlug() and !is_null($id)){
                $this->item = $this->item->findBySlug($id) ?? new $name;
            }
        }

        $this->item->setParameters($this->parameters);
        $this->item->setBlock($this->id);
        $this->item->setup();

        foreach($this->addons as $addon){
            $this->{$addon->name} = AddOn::find($addon->id);
        }

        return $this;
    }

    /**
     * Unsettle model data
     *
     * @return Block
     */
    public function unsettle(): Block {
        $this->item = null;

        return $this->unsettleAddons();
    }

    /**
     * Unsettle all addons from the model
     *
     * @return Block
     */
    public function unsettleAddons(): Block {
        foreach($this->addons as $addon){
            unset($this->{$addon->name});
        }

        return $this;
    }

    public function form() {
        $form = $this->item->form();
        if(!is_null($form) and is_null($form->getElement('block_id'))){
            $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->id]));
            $form->add(FormElement::hidden(['name'=>'id', 'value'=>$this->item->id]));
        }

        return $form;
    }

    public function relationsForm($relBlock) {
        $form = $this->item->relationsForm($relBlock);

        return $form;
    }

    /**
     * Return form to create a new block
     *
     * @return Form
     * @throws Exception
     */
    public function itemForm():Form {
        return $this->settingsForm();
    }

    /**
     * Return form to create a new block or update existing one
     *
     * @return Form
     * @throws Exception
     */
    public function settingsForm(): Form {
        $form = new Form('/settings/block/setup');

        if(empty($form->elements())){
            $blockGroup = new FormGroup('blockGroup', __('block.createForm.blockData'), ['class'=>'fullWidth twoColumns']);
            $blockGroup->add(FormElement::hidden(['name'=>'panelLocation', 'value'=>$this->panelLocation]));
            $blockGroup->add(FormElement::hidden(['name'=>'block_id', 'value'=>@$this->id]));
            $blockGroup->add(FormElement::hidden(['name'=>'page_id', 'value'=>$this->page_id]));
            $blockGroup->add(FormElement::select(['name'=>'type', 'label'=>__('block.createForm.type'), 'value'=>@$this->type, 'options'=>$this->allBlocksSelect()]));
            if(!is_null($this->id)){
                $blockGroup->element("type")->setParameter("disabled", "disabled");
            }
            else{
                $blockGroup->element("type")->obligatory();
            }
            $blockGroup->add(FormElement::select(['name'=>'width', 'label'=>__('block.createForm.width'), 'value'=>$this->width ?? 12, 'options'=>[3=>"25%", 4=>"33%", 6=>"50%", 8=>"67%", 9=>"75%", 12=>"100%"]])
                ->obligatory()
            );
            $blockGroup->add(FormElement::text(['name'=>'title', 'label'=>__('settings.common.title'), 'value'=>$this->getTranslations('title'), 'translate'=>true]));
            $blockGroup->add(FormElement::text(['name'=>'name', 'label'=>__('settings.common.name'), 'value'=>@$this->name]));
            $blockGroup->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->getTranslations('description'), 'translate'=>true]));

            $form->addGroup($blockGroup);

            if(Auth::user()->role == "master"){
                $viewGroup = new FormGroup('viewGroup', __('block.createForm.blockView'), ['class'=>'fullWidth twoColumns']);

                $viewGroup->add(FormElement::text(['name'=>'layoutClass', 'label'=>__('block.createForm.layoutClass'), 'value'=>$this->layoutClass ?? 'primary']));
                $viewGroup->add(FormElement::text(['name'=>'cssClass', 'label'=>__('block.createForm.cssClass'), 'value'=>@$this->cssClass]));
                $form->addGroup($viewGroup);
            }
            $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        }

        return $form;
    }

    public function blockForm() {
        $form = $this->panelForm();
        $form->setAction('/admin/settings/block/setup');

        return $form;
    }

    public function allBlocksSelect() {
        $blocks = BlockList::all();

        $select = [];
        foreach($blocks as $block){
            $select[$block->block] = __('block.type.'.$block->block.'.title');
        }

        return $select;
    }

    public function content() {
        return $this->item()->content();
    }

    /**
     * Return first item from content
     *
     * @return mixed
     */
    public function firstItem(){
        return $this->content()->first();
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
     * @throws Exception
     */
    public function parentItem(){
        $parentBlock = $this->parentBlock()->specify()->item();

        $itemId = DB::table($parentBlock->getTable()."_blocks")->where("block_id", $this->id)->get(["modelItem_id"]);

        if(!$itemId->isEmpty()){
            return $parentBlock::find($itemId->first()->modelItem_id);
        }

        return null;
    }

    /**
     * @param string $requestType
     * @return string
     */
    public function findValidationRequest(string $requestType = 'Change'): string {
        if($requestType == 'Public'){
            $publicity = 'Visitor';
        }
        else {
            $publicity = 'Admin';
        }

        $validatorClass =  "\\App\\Just\\Requests\\Block\\" . $publicity . "\\" . $requestType . Str::ucfirst( Str::singular($this->type) ) . 'Request';

        if (!class_exists($validatorClass)) {
            $validatorClass =  "\\Just\\Requests\\Block\\" . $publicity . "\\" . $requestType . Str::ucfirst( Str::singular($this->type) ) . 'Request';
        }

        return $validatorClass;
    }

    public function handleItemSetupForm(Request $request, $isPublic = false) {
        if(!$isPublic) {
            $method = 'handleItemForm';

            // looking for a custom request validator
            $validatorClass = $this->findValidationRequest();
        }
        else{
            $method = 'handlePublicForm';

            // looking for a custom request validator
            $validatorClass = $this->findValidationRequest('Public');
        }

        $validatedRequest = new $validatorClass;
        foreach($request->all() as $param=>$val){
            $validatedRequest->{$param} = $val;
        }
        if($validatorClass != 'Illuminate\Http\Request' and $validatedRequest->authorize()){
            $addonValidators = [];
            foreach ($this->addons as $addon){
                $addonValidators += $addon->validationRules();
            }

            $validator = \Validator::make($request->all(),
                    $validatedRequest->rules() + $addonValidators + [
                        "id" => "integer|min:1|nullable",
                        'block_id' => "required|integer|min:1"
                    ],
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
                    $validatedRequest->query->set($name, $param);
                }
            }
        }
        elseif($validatorClass == 'Illuminate\Http\Request'){
            $validatedRequest = $request;
        }

        return $this->item->{$method}($validatedRequest);
    }

    /**
     * Handle the creation or updating settings form
     *
     * @param ValidateRequest $request
     * @return $this
     */
    public function handleSettingsForm(ValidateRequest $request): Block {
        if(is_null($this->id)){
            $this->type = $request->type;
        }
        $this->panelLocation = $request->panelLocation;
        $this->page_id = $request->page_id;
        $this->name = $request->name;
        $this->title = $request->title ?? "";
        $this->description = $request->description ?? "";
        $this->width = $request->width ?? ( $this->width ?? 12 );
        $this->layoutClass = (Auth::user()->role == "master" ? $request->layoutClass : $this->layoutClass) ?? 'primary';
        $this->cssClass = (Auth::user()->role == "master" ?  $request->cssClass : $this->cssClass) ?? '';
        $this->orderNo = $this->orderNo ?? Useful::getMaxNo($this->table, ['panelLocation' => $request->panelLocation, "page_id"=>$request->page_id]);
        $this->parameters = json_decode('{}');

        $this->save();

        return $this;
    }

    /**
     * Handle the cropping of the image related to the block
     *
     * @param Request $request
     * @return Image
     * @throws ReflectionException
     */
    public function handleCrop(Request $request): Image {
        $reflection = new ReflectionMethod($this->item, 'handleCrop');
        $validatorClass = $reflection->getParameters()[0]->getClass()->name;

        $validatedRequest = new $validatorClass;
        if($validatedRequest->authorize()){
            $validData = $request->validate($validatedRequest->rules()+['block_id' => "required|integer|min:1"], $validatedRequest->messages());
            foreach($validData as $name=>$param){
                $validatedRequest->request->set($name, $param);
                $validatedRequest->query->set($name, $param);
            }
        }

        return $this->item->handleCrop($validatedRequest);
    }

    public function deleteItem() {
        //Delete whole block
        if(is_null($this->item->id)){
            $this->deleteImage($this->item);
            $this->delete();
        }
        // Delete model item in the block
        else{
            $this->deleteImage($this->item);
            $this->item->delete();
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
            return null;
        }

        $block->specify($id);

        return $block;
    }

    public static function findByName($name){
        $block = self::where('name', $name)->get();

        if($block->isEmpty()){
            return null;
        }

        return $block->first();
    }

    /**
     * Return block item
     *
     * @return BlockItem|null
     * @throws Exception
     */
    public function item(): ?BlockItem {
        if($this->item === null){
            $this->specify();
        }

        return $this->item;
    }

    /**
     * @return HasMany
     * @throws Exception
     */
    public function items(): HasMany {
        return $this->hasMany($this->itemClassName());
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function itemClassName(): string {
        $name = "\\App\\Just\\Models\\Blocks\\". ucfirst($this->type);

        // looking for a custom block
        if(!class_exists($name)){
            $name = "\\Just\\Models\\Blocks\\". ucfirst($this->type);
            if(!class_exists($name)){
                throw new Exception("Block class \"".ucfirst($this->type)."\" not found");
            }
        }

        return $name;
    }

    public function move($dir) {
        // Move whole block
        if(is_null($this->item->id)){
            $where = [
                    'panelLocation' => $this->panelLocation,
                    'page_id' => $this->page_id
                ];

            return Useful::moveModel($this->unsettle(), $dir, $where);
        }
        // Move model item in the block
        else{
            return $this->item->move($dir);
        }
    }

    public function moveTo($newPosition) {
        // Move whole block
        if(is_null($this->item->id)){
            $where = [
                    'panelLocation' => $this->panelLocation,
                    'page_id' => $this->page_id
                ];

            return Useful::moveModelTo($this, $newPosition, $where);
        }
        // Move model item in the block
        else{
            $this->item->moveTo($newPosition);
        }
    }

    public function visibility($visibility) {
        if(is_null($this->item->id)){
            $model = $this->unsettle();
        }
        else{
            $model = $this->item;
        }

        $model->isActive = $visibility;
        $model->save();

        return $model;
    }

    public function isSetted() {
        $isSetted = true;
        foreach($this->item->neededParameters() as $param){
            if(!isset($this->parameters->{$param})){
                $isSetted = false;
                break;
            }
        }

        return $isSetted;
    }

    /**
     * Return form to customize the block
     *
     * @return mixed
     * @throws Exception
     */
    public function customizationForm() {
        return $this->item()->customizationForm($this);
    }

    public function parameter($param) {
        return @$this->parameters->{$param};
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
     * Return add-ons related to the current block
     *
     * @return HasMany
     */
    public function addons(): HasMany {
        return $this->hasMany(AddOn::class)->where('isActive', 1);
    }

    /**
     * Find related to current block add-on by name
     *
     * @param string $name add-on name
     * @return mixed
     */
    public static function addon(string $name) {
        return AddOn::where('name', $name)->first();
    }

    /**
     * Return panel where block is located
     *
     * @return BelongsTo
     */
    public function panel(): BelongsTo {
        return $this->belongsTo(Panel::class, 'panelLocation', 'location');
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

    public function categories(): Collection {
        return $this->addons()->where('type', 'categories');
    }

    public function phrases(): Collection {
        return $this->addons()->where('type', 'phrase');
    }

    public function images(): Collection {
        return $this->addons()->where('type', 'image');
    }

    public function paragraphs(): Collection {
        return $this->addons()->where('type', 'paragraph');
    }

    public function currentCategory() {
        if(is_null($this->currentCategory)){
            $this->currentCategory = Categories::where('value', request('category'))->first();
        }

        return $this->currentCategory;
    }

    /**
     * Page belongs to the current block
     *
     *
     */
    public function page() {
        $page = $this->belongsTo(Page::class)->first();

        // event block located in the related block
        if(is_null($page) and is_null($this->panelLocation)){
            $page = $this->parentBlock()->page();
        }
        // event block located in header or footer
        elseif(!is_null($this->panelLocation)){
            $page = Page::first();
        }

        return $page;
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

    public function itemImage(): ?string {
        return null;
    }

    public function itemIcon(): ?string {
        return null;
    }

    public function itemText(): ?string {
        return null;
    }

    /**
     * Return caption for block item in the block list
     *
     * @return string
     */
    public function itemCaption(): string {
        return ($this->title == '' ? __('block.untitled') : $this->title) . ' (' . $this->type . ')';
    }
}
