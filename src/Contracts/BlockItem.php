<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Contracts;

use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;

/**
 * Interface BlockItem
 * @package Just\Contracts
 *
 * @property int $id
 */
interface BlockItem {

    public function itemForm(): Form;

    public function handleItemForm(ValidateRequest $request);

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

    /**
     * Return table name for the block item
     */
    public function getTable();

}
