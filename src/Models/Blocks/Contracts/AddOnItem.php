<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Models\Blocks\Contracts;

use Just\Models\AddOn;
use Lubart\Form\Form;

interface AddOnItem {

    /**
     * Update existing block form and add new elements
     *
     * @param Form $form
     * @param $values
     * @return Form
     */
    public function updateForm(Form $form, $values): Form;

    /**
     * Handle addon values in the existing block form
     *
     * @param ValidateRequest $request
     * @param BlockItem $blockItem
     * @return mixed
     */
    public function handleForm(ValidateRequest $request, BlockItem $blockItem);

    /**
     * Validation rules to the addon elements in the block form
     *
     * @param AddOn $addon
     * @return array
     */
    public function validationRules(AddOn $addon): array;

}
