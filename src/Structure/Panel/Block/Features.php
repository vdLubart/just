<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Models\Icon;
use Lubart\Just\Models\IconSet;
use Illuminate\Http\Request;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Lubart\Just\Structure\Panel\Block;

class Features extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'icon_id', 'title', 'description', 'link', 'orderNo', 'isActive'
    ];
    
    protected $table = 'features';

    protected $neededParameters = [ 'itemsInRow' ];

    public function setup() {
        if(!Useful::isRouteExists("iconset/{id}/{page?}")){
            JustRoute::create([
                'route' => "iconset/{id}/{page?}",
                'type' => 'ajax',
                'block_id' => $this->block_id,
                'action' => 'iconset'
            ]);
        }
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $currentIcon = Icon::find($this->icon_id);
        $this->form->add(FormElement::html(['name'=>'currentIcon', 'value'=>'<div id="currentIcon">'.(!empty($currentIcon)? '<'.$currentIcon->iconSet->tag.' class="'.$currentIcon->iconSet->class.' '.$currentIcon->class.'"></'.$currentIcon->iconSet->tag.'>' : '').'</div>', 'label'=>'Current icon']));
        $this->form->add(FormElement::select(['name'=>'iconSet', 'label'=>'Choose icon set', 'options'=> IconSet::getList(), 'value'=>isset($this->icon)?$this->icon->set_id:null]));
        $this->form->add(FormElement::html(['name'=>'divicon', 'value'=>'<div id="icons"></div>', 'label'=>'Choose icon']));
        $this->form->add(FormElement::hidden(['name'=>'icon', 'value'=>$this->icon_id]));
        if(empty($this->parameter('ignoreCaption'))){
            $this->form->add(FormElement::text(['name'=>'title', 'label'=>__('settings.common.title'), 'value'=>$this->title]));
        }
        if(empty($this->parameter('ignoreDescription'))){
            $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->description]));
        }
        $this->form->add(FormElement::text(['name'=>'link', 'label'=>__('features.link'), 'value'=>$this->link]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        $this->form->useJSFile('/js/blocks/features/settingsForm.js');
        
        return $this->form;
    }
    
    public function setupForm(Block $block) {
        $form = new Form('/admin/settings/setup');
        
        $form->setType('setup');
        
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$block->id]));
        
        $settingsViewGroup = new FormGroup('settingsView', __('block.preferences.settingsView.title'), ['class'=>'col-md-6']);
        $settingsViewGroup->add(FormElement::select(['name'=>'itemsInRow', 'label'=>__('features.preferences.itemScale'), 'value'=>@$block->parameters->itemsInRow, 'options'=>[3=>trans_choice('feature.preferences.itemScaleValue', 4, ['items'=>4]), 4=>trans_choice('feature.preferences.itemScaleValue', 3, ['items'=>3]), 6=>trans_choice('feature.preferences.itemScaleValue', 2, ['items'=>2]), 12=>trans_choice('feature.preferences.itemScaleValue', 1, ['items'=>1])]]));
        $form->addGroup($settingsViewGroup);

        if(\Auth::user()->role == "master"){
            $this->addIgnoretCaptionSetupGroup($form);
        }
        
        $submitGroup = new FormGroup('submitSetup', '', ['class'=>'col-md-12 clear']);
        $submitGroup->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        $form->addGroup($submitGroup);
        
        return $form;
    }
    
    public function handleForm(ValidateRequest $request) {
        if(is_null($request->get('id'))){
            $feature = new Features;
            $feature->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->get('block_id')]);
        }
        else{
            $feature = Features::findOrNew($request->get('id'));
        }
        $feature->setBlock($request->get('block_id'));
        
        $feature->icon_id = $request->get('icon');
        $feature->title = $request->get('title');
        $feature->description = $request->get('description');
        $feature->link = $request->get('link');
        $feature->save();
        
        $this->handleAddons($request, $feature);
        
        return $feature;
    }
    
    /**
     * Get feature icon
     * 
     * @return Builder
     */
    public function icon() {
        return $this->belongsTo(Icon::class);
    }
    
    /**
     * Build icon list in AJAX request
     * 
     * @param Request $request
     */
    public function iconset(Request $request) {
        return Useful::paginate(Icon::with('iconSet')->where('icons.icon_set_id', $request->id)->get(), 25, $request->page);
    }
}
