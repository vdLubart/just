<?php

namespace Just\Models\Blocks\AddOns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Just\Contracts\AddOnItem;
use Just\Contracts\BlockItem;
use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;
use Just\Models\AddOn;
use Illuminate\Support\Facades\DB;

/**
 * Class AbstractAddOn
 * @package Just\Models\Blocks\AddOns
 *
 * @property AddOn $addon
 * @property int $id
 * @property int $add_on_id
 * @property mixed $value
 */
abstract class AbstractAddOn extends Model implements AddOnItem
{
    /**
     * Update existing block form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    abstract public function updateForm(BlockItem $blockItem): Form;

    /**
     * Handle addon values in the existing block form
     *
     * @param ValidateRequest $request
     * @param BlockItem $blockItem
     */
    public function handleForm(ValidateRequest $request, BlockItem $blockItem) {
        $this->add_on_id = $this->addon->id;
        $this->value = $request->{$this->addon->name."_".$this->addon->id};

        $this->save();

        if($this->wasRecentlyCreated){
            DB::table($blockItem->getTable()."_".$this->getTable())
                ->insert([
                    'modelItem_id' => $blockItem->id,
                    'addonItem_id' => $this->id
                ]);
        }
    }

    /**
     * Validation rules to the addon elements in the block form
     *
     * @param AddOn $addon
     * @return array
     */
    abstract public function validationRules(AddOn $addon): array;

    /**
     * Related addon
     *
     * @return BelongsTo
     */
    public function addon(): BelongsTo {
        return $this->belongsTo(AddOn::class, "add_on_id", "id");
    }
}
