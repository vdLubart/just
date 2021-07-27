<?php

namespace Just\Models\Blocks\AddOns;

use Just\Contracts\BlockItem;
use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;
use Lubart\Form\FormGroup;

/**
 * @mixin IdeHelperCategories
 */
class Categories extends AbstractAddOn
{
    use HasTranslations;

    protected $table = 'categories';

    protected $fillable = ['addon_id', 'title', 'value'];

    public $translatable = ['title'];

    /**
     * Update existing settings form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    public function updateForm(BlockItem $blockItem): Form {
        $blockItem->form()->add(FormElement::select(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'options'=>$this->addon->valuesSelectArray('title'), 'value'=>$this->value]));

        return $blockItem->form();
    }

    public function handleForm_shouldBeUpdated(ValidateRequest $request, BlockItem $item) {
        $addon = $this->addon;
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

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => "required|integer|min:1",
        ];
    }

    /**
     * Get page settings form
     *
     * @return Form
     * @throws \Exception
     */
    public function settingsForm() {
        $form = new Form('admin/settings/category/setup');

        $addons = AddOn::where('type', 'categories')->pluck('title', 'id');

        $form->add(FormElement::hidden(['name'=>'category_id', 'value'=>@$this->id]));
        $addOnGroup = new FormGroup('addOnGroup', __('addOn.category.createForm.addOnGroup'), ['style'=>'flex: 1 0 100%']);
        $addOnGroup->add(FormElement::select(['name'=>'addon_id', 'label'=>__('addOn.category.createForm.addOn'), 'value'=>@$this->addon_id, 'options'=>$addons]));
        $form->addGroup($addOnGroup);

        $pairGroup = new FormGroup('pairGroup', __('addOn.category.createForm.pairGroup'), ['style'=>'margin-top:50px', 'class'=>'twoColumns']);
        $pairGroup->add(FormElement::text(['name'=>'title', 'label'=>__('addOn.category.createForm.caption'), 'value'=>@$this->title]));
        $pairGroup->add(FormElement::text(['name'=>'value', 'label'=>__('addOn.category.createForm.value'), 'value'=>@$this->value]));
        $form->addGroup($pairGroup);

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
