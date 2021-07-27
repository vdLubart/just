<?php

namespace Just\Models\Blocks;

use Lubart\Form\Form;
use Lubart\Form\FormElement;

/**
 * @mixin IdeHelperHtml
 */
class Html extends Text
{
    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form;
        }

        $this->identifyItemForm();

        $this->form->add(FormElement::textarea([
            'name'=>'text',
            'label'=>__('html.title'),
            'value'=>$this->getTranslations('text'),
            'translate'=>true,
            'richEditor'=>false
        ])
            ->obligatory()
        );

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }
}
