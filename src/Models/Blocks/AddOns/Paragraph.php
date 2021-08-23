<?php

namespace Just\Models\Blocks\AddOns;

use Just\Contracts\BlockItem;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperParagraph
 */
class Paragraph extends AbstractAddOn
{
    use HasTranslations;

    protected $table = 'paragraphs';

    protected $fillable = ['add_on_id', 'value'];

    public array $translatable = ['value'];

    /**
     * Update existing block form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    public function updateForm(BlockItem $blockItem): Form {
        $blockItem->form()->add(FormElement::textarea(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'value'=>$this->getTranslations('value'), 'translate'=>true]));

        return $blockItem->form();
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => ($addon->isRequired?"required":"nullable") . "|array",
        ];
    }
}
