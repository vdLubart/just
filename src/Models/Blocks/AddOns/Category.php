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
 * @mixin IdeHelperCategory
 */
class Category extends AbstractAddOn
{
    use HasTranslations;

    protected $table = 'categories';

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
        $blockItem->form()->add(FormElement::select(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'options'=>$options, 'value'=>$this->value]));

        return $blockItem->form();
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => ($addon->isRequired?"required":"nullable") . "|integer|min:1",
        ];
    }
}
