<?php

namespace Just\Models\Blocks\AddOns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Just\Contracts\Requests\ValidateRequest;
use Just\Tools\Useful;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperCategoryOption
 *
 * @property AddOn $addOn
 */
class AddOnOption extends Model
{
    use HasTranslations;

    protected $table = 'addonOptions';

    protected $fillable = ['add_on_id', 'option', 'isActive'];

    public array $translatable = ['option'];

    public function itemForm(): Form {
        $form = new Form('/settings/add-on-option/category/option/setup');

        $form->add(FormElement::hidden(['name'=>'id', 'value'=>@$this->id]));

        $categories = [];
        foreach(AddOn::whereIn('type', ['category', 'tag'])->get() as $addon){
            $categories[$addon->id] = __('addOn.addOnLocation', ['addOn' => $addon->title, 'block' => $addon->block->title, 'page' => $addon->block->page()->title]);
        }

        $form->add(FormElement::select(['name'=>'add_on_id', 'label'=>__('addOnOption.createForm.addOn'), 'options'=>$categories, 'value'=>@$this->add_on_id])
            ->obligatory()
        );

        $form->add(FormElement::text(['name'=>'option', 'label'=>__('addOnOption.createForm.option'), 'value'=>$this->getTranslations('option'), 'translate'=>true])
            ->obligatory()
        );

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }

    public function handleSettingsForm(ValidateRequest $request) {
        $categoryOption = $this->findOrNew($request->id);

        $categoryOption->add_on_id = $request->add_on_id;
        $categoryOption->option = $request->option;

        $categoryOption->save();
    }

    public function move($dir) {
        $where = [
            'add_on_id' => $this->add_on_id
        ];

        return Useful::moveModel($this, $dir, $where);
    }

    public function moveTo($newPosition) {
        $where = [
            'add_on_id' => $this->add_on_id
        ];

        return Useful::moveModelTo($this, $newPosition, $where);
    }

    /**
     * @return BelongsTo
     */
    public function addOn(): BelongsTo {
        return $this->belongsTo(AddOn::class);
    }

}
