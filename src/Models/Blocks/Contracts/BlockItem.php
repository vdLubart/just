<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Models\Blocks\Contracts;

use Lubart\Form\Form;

interface BlockItem {

    public function settingsForm(): Form;

    public function handleSettingsForm(ValidateRequest $request);

    /**
     * Return image code for the list item
     *
     * @return string
     */
    public function itemImage(): ?string;

    /**
     * Return icon code for the list item
     *
     * @return string
     */
    public function itemIcon(): ?string;

    /**
     * Return short text description for the list item
     *
     * @return string
     */
    public function itemText(): ?string;

    /**
     * Return caption for the list item
     *
     * @return string
     */
    public function itemCaption(): ?string;

}