<?php

namespace Just\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Just\Models\Blocks\AddOns\AbstractAddOn;
use Just\Models\Blocks\Contracts\AddOnItem;
use Just\Models\Blocks\Contracts\BlockItem;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Lubart\Form\Form;
use Illuminate\Support\Facades\Schema;
use Just\Models\System\AddonList;
use Lubart\Form\FormElement;
use Just\Tools\Useful;
use Illuminate\Support\Facades\Artisan;
use Just\Requests\SaveAddonRequest;
use Spatie\Translatable\HasTranslations;

/**
 * Class AddOn
 *
 * @package Just\Models
 * @property AddOnItem $addonItem
 * @mixin IdeHelperAddOn
 */
class AddOn extends Model
{
    use HasTranslations;

    protected $table = 'addons';

    protected $fillable = ['block_id', 'type', 'name', 'title', 'description', 'orderNo', 'isActive', 'parameters'];

    public $translatable = ['title', 'description'];

    /**
     * Class name of the current addon
     *
     * @return string
     * @throws Exception
     */
    public function addonItemClassName(): string {
        $class = "\\Just\\Models\\Blocks\\AddOns\\".ucfirst($this->type);

        if(!class_exists($class)){
            $class = "\\App\\Just\\Models\\Blocks\\AddOns\\". ucfirst($this->type);
            if(!class_exists($class)){
                throw new Exception("Add-on class not found");
            }
        }

        return $class;
    }

    /**
     * Return related addon item
     *
     * @return HasOne
     * @throws Exception
     */
    public function item(): HasOne {
        return $this->hasOne($this->addonItemClassName());
    }

    /**
     * @param BlockItem $blockItem
     * @return AddOnItem
     * @throws Exception
     */
    public function addonItem(BlockItem $blockItem): AddOnItem {
        return $blockItem->belongsToMany($this->addonItemClassName(), $blockItem->getTable()."_".Str::plural($this->type), 'modelItem_id', 'addonItem_id')->where('add_on_id', $this->id)->first() ?? $this->newAddOnItem();
    }

    /**
     * Create new addon item instance
     *
     * @return AbstractAddOn
     * @throws Exception
     */
    protected function newAddOnItem(): AddOnItem {
        $addonItemClass = $this->addonItemClassName();
        $addonItem = new $addonItemClass;
        $addonItem->add_on_id = $this->id;

        return $addonItem;
    }

    /**
     * Block belongs to the current addon
     *
     * @return BelongsTo
     */
    public function block(): BelongsTo {
        return $this->belongsTo(Block::class);
    }

    /**
     * Update existing settings form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     * @throws Exception
     */
    public function updateForm(BlockItem $blockItem): Form {
        return  $this->addonItem($blockItem)->updateForm($blockItem);
    }

    /**
     * Treat form elements related to the addon
     *
     * @param ValidateRequest $request
     * @param BlockItem $blockItem Block model item
     * @return mixed
     * @throws Exception
     */
    public function handleForm(ValidateRequest $request, BlockItem $blockItem) {
        return $this->addonItem($blockItem)->handleForm($request, $blockItem);
    }

    /**
     * Validation rules to the addon elements in the block form
     *
     * @return array
     * @throws Exception
     */
    public function validationRules(): array {
        return $this->newAddOnItem()->validationRules($this);
    }

    public function valuesSelectArray($value, $key='id'): array {
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
     * Return form to create a new add-on
     *
     * @return Form
     * @throws Exception
     */
    public function itemForm(): Form {
        return $this->settingsForm();
    }

    /**
     * Get page settings form
     *
     * @return Form
     * @throws Exception
     */
    public function settingsForm(): Form {
        $form = new Form('/settings/add-on/setup');

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
            $form->element("type")->setParameter("disabled", "disabled");
            $form->element("block_id")->setParameter("disabled", "disabled");
        }
        $form->add(FormElement::text(['name'=>'title', 'label'=>__('addOn.createForm.userTitle'), 'value'=>$this->getTranslations('title'), 'translate'=>true])
            ->obligatory()
        );
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->getTranslations('description'), 'translate'=>true]));

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }

    public function handleSettingsForm(SaveAddonRequest $request) {
        $this->title = $request->title;
        $this->description = $request->description;
        $this->name = $request->name;

        if(is_null($this->id)){
            $this->block_id = $request->block_id;
            $this->type = $request->type;
            $this->orderNo = Useful::getMaxNo('addons', ['block_id'=>$request->block_id]);

            $modelTable = Block::find($request->block_id)->specify()->item()->getTable();
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

    public function itemCaption(): ?string {
        return $this->title . " (" . $this->type . ")";
    }

    public function move($dir) {
        $where = [
            'block_id' => $this->block_id
        ];

        return Useful::moveModel($this, $dir, $where);
    }

    public function moveTo($newPosition) {
        $where = [
            'block_id' => $this->block_id
        ];

        return Useful::moveModelTo($this, $newPosition, $where);
    }
}
