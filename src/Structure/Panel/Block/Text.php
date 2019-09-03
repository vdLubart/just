<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Requests\TextChangeRequest;

class Text extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
    ];
    
    protected $table = 'texts';
    
    protected $settingsTitle = 'Plain Text';

    public function __construct() {
        parent::__construct();

        $this->settingsTitle = __('text.title');
    }
    
    /**
     * Return item form
     * 
     * @return \Lubart\Form\Form
     */
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>__('text.text'), 'value'=>@$this->text]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        $this->form->useJSFile('/js/blocks/text/settingsForm.js');
        
        return $this->form;
    }
    
    public function handleForm(TextChangeRequest $request) {
        if(is_null($request->request->get('id'))){
            $text = new Text;
            $text->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->request->get('block_id')]);
            $text->setBlock($request->get('block_id'));
        }
        else{
            $text = Text::findOrNew($request->request->get('id'));
        }
        
        $text->text = $request->request->get('text');
        $text->save();
        
        $this->handleAddons($request, $text);

        return $text;
    }
}
