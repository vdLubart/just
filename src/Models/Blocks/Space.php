<?php

namespace Just\Models\Blocks;

use Exception;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;

/**
 * @mixin IdeHelperSpace
 */
class Space extends AbstractItem
{
    protected array $neededParameters = [
        'height'    => 'Block height'
    ];

    /**
     * $table var is a mock to keep general block functionality.
     * In fact this block never updates texts table, but can read it.
     *
     * @var string $table
     */
    protected $table = 'texts';

    public function content() {
        return collect([]);
    }

    /**
     * Return item form
     *
     * @return Form
     * @throws Exception
     */
    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form;
        }

        $this->form->add(FormElement::html(['value'=>'This block cannot have any item']));

        return $this->form;
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

        foreach($this->neededParameters() as $param=>$label){
            $form->add(FormElement::text(['name'=>$param, 'label'=>__('space.'.$param), 'value'=>@$this->block->parameters->{$param}]));
        }

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }

    public function handleItemForm(ValidateRequest $request) {}
}
