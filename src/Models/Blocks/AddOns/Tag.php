<?php

namespace Just\Models\Blocks\AddOns;

use Just\Contracts\BlockItem;
use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperCategory
 */
class Tag extends AbstractAddOn
{
    use HasTranslations;

    protected $table = 'tags';

    protected $fillable = ['addon_id', 'value'];

    /**
     * Update existing settings form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    public function updateForm(BlockItem $blockItem): Form {
        $options = [];
        foreach ($this->addon->options as $option){
            $options[$option->id] = $option->option;
        }
        $values = json_decode($this->value, true);
        $blockItem->form()->add(FormElement::select(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'options'=>$options, 'value'=>$values, 'multiple'=>true]));

        return $blockItem->form();
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => ($addon->isRequired?"required":"nullable") . "|array",
        ];
    }

    public function handleForm(ValidateRequest $request, BlockItem $blockItem) {
        $this->add_on_id = $this->addon->id;
        $this->value = json_encode($request->{$this->addon->name."_".$this->addon->id});

        $this->save();

        if($this->wasRecentlyCreated){
            DB::table($blockItem->getTable()."_".$this->getTable())
                ->insert([
                    'modelItem_id' => $blockItem->id,
                    'addonItem_id' => $this->id
                ]);
        }
    }
}
