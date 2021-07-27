<?php

namespace Just\Models\Blocks;

use Exception;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Contracts\Requests\ValidateRequest;
use Just\Tools\Useful;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperText
 */
class Text extends AbstractItem
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'orderNo'
    ];

    public array $translatable = ['text'];

    protected $table = 'texts';

    /**
     * Return item form
     *
     * @return Form
     * @throws
     */
    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form;
        }

        $this->identifyItemForm();

        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>__('text.text'), 'value'=>$this->getTranslations('text'), 'translate'=>true])
            ->obligatory()
        );

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }

    /**
     * @throws Exception
     */
    public function handleItemForm(ValidateRequest $request) {
        if(is_null($request->id)){
            $text = new Text;
            $text->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->block_id]);
            $text->setBlock($request->block_id);
        }
        else{
            $text = Text::findOrNew($request->id);
        }

        $text->text = $request->text;
        $text->save();

        $this->handleAddons($request, $text);

        return $text;
    }

    public function itemText(): string {
        return substr(strip_tags($this->text), 0, 100);
    }
}
