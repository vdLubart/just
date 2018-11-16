<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Lubart\Form\FormElement;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Tools\Useful;

class Link extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'block_id', 'linkedBlock_id', 'orderNo', 'isActive'
    ];
    
    protected $table = 'links';
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $blocks = [];
        foreach(Block::where('name', '<>', 'link')->get() as $block){
            $blocks[$block->id] = $block->title . "(".$block->name.") at ".(is_null($block->page)?$block->panelLocation:$block->page->title ." page");
        }
        
        $this->form->add(FormElement::select(['name'=>'linkedBlock_id', 'label'=>'Linked Block', 'value'=>@$this->linkedBlock_id, 'options'=>$blocks]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
            
        return $this->form;
    }
    
    public function handleForm(Request $request) {
        if(is_null($request->request->get('id'))){
            $link = new Link;
            $link->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
            $link->setBlock($request->get('block_id'));
        }
        else{
            $link = Link::findOrNew($request->request->get('id'));
        }
        
        $link->linkedBlock_id = $request->get('linkedBlock_id');
        $link->save();
        
        $this->handleAddons($request, $link);
        
        return $link;
    }
    
    public function linkedBlock() {
        return Block::find($this->linkedBlock_id)->specify();
    }
}
