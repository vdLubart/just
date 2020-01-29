<?php

namespace Just\Models\Blocks;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Spatie\Translatable\HasTranslations;

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

    public $translatable = ['text'];
    
    protected $table = 'texts';
    
    /**
     * Return item form
     * 
     * @return \Lubart\Form\Form
     * @throws
     */
    public function settingsForm(): Form {
        if(is_null($this->form)){
            return new Form;
        }

        $this->identifySettingsForm();
        
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>__('text.text'), 'value'=>$this->getTranslations('text'), 'translate'=>true])
            ->obligatory()
        );
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        $this->form->useJSFile('/js/blocks/text/settingsForm.js');
        
        return $this->form;
    }
    
    public function handleSettingsForm(ValidateRequest $request) {
        if(is_null($request->request->get('id'))){
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
