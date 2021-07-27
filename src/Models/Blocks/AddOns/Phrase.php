<?php

namespace Just\Models\Blocks\AddOns;

use Just\Models\Blocks\Contracts\BlockItem;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;
use Spatie\Translatable\HasTranslations;

class Phrase extends AbstractAddOn
{
    use HasTranslations;

    protected $table = 'phrases';

    protected $fillable = ['add_on_id', 'value'];

    public $translatable = ['value'];

    /**
     * Update existing block form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    public function updateForm(BlockItem $blockItem): Form {
        $blockItem->form()->add(FormElement::text(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'value'=>$this->getTranslations('value'), 'translate'=>true]));

        return $blockItem->form();
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => "required|array",
        ];
    }
}
