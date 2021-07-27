<?php

namespace Just\Models\Blocks;

use Just\Models\Block;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Tools\Useful;

/**
 * @mixin IdeHelperLink
 */
class Link extends AbstractItem
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

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        $blocks = [];
        foreach(Block::where('type', '<>', 'link')->get() as $block){
            $blocks[$block->id] = __("link.block", ['block'=>$block->title, 'type'=>$block->type, 'page'=>(is_null($block->page())?$block->panelLocation:$block->page()->title)]);
        }

        $this->form->add(FormElement::select(['name'=>'linkedBlock_id', 'label'=>__('link.linkedBlock'), 'value'=>@$this->linkedBlock_id, 'options'=>$blocks]));

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }

    public function handleItemForm(ValidateRequest $request) {
        if(is_null($request->get('id'))){
            $link = new Link;
            $link->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
            $link->setBlock($request->get('block_id'));
        }
        else{
            $link = Link::findOrNew($request->get('id'));
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
