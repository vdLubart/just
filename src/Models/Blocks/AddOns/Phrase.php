<?php

namespace Just\Models\Blocks\AddOns;

use Exception;
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
     * @param Form $form Form object
     * @param mixed $values
     * @return Form
     * @throws Exception
     */
    public function updateForm(Form $form, $values): Form {
        $values = $values->isEmpty() ? [] : $values->first()->getTranslations('value');
        $form->add(FormElement::text(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title, 'value'=>$values, 'translate'=>true]));

        return $form;
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => "required|array",
        ];
    }
}
