<?php

namespace Lubart\Just\Structure;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Support\Facades\DB;
use Lubart\Just\Requests\ChangeLayoutRequest;
use Lubart\Just\Models\Theme;

class Layout extends Model
{
    protected $table = 'layouts';
    
    protected $fillable = ['name', 'class', 'width'];
    
    public function panels() {
        return $this->belongsTo(Panel::class, 'id', 'layout_id')
                ->orderBy('panels.orderNo')
                ->get();
    }
    
    /**
     * Get layout settings form
     * 
     * @return Form
     */
    public function settingsForm() {
        $form = new Form('admin/settings/layout/setup');
        
        $form->setType("layoutSettings");
        
        $form->add(FormElement::hidden(['name'=>'layout_id', 'value'=>$this->id]));
        $form->add(FormElement::select(['name'=>'name', 'label'=>__('layout.createForm.themeTitle'), 'value'=>$this->name ?? Theme::where('isActive', 1)->first()->name, 'options'=>Theme::all()->pluck('name', 'name')]));
        $form->add(FormElement::text(['name'=>'class', 'label'=>__('layout.createForm.themeClass'), 'value'=>$this->class ?? 'primary']));
        if($this->id == 1){
            $form->getElement("name")->setParameters("disabled", "disabled");
            $form->getElement("class")->setParameters("disabled", "disabled");
        }
        $form->add(FormElement::number(['name'=>'width', 'label'=>__('layout.createForm.width'), 'value'=>$this->width ?: '1920']));
        
        $form->add(FormElement::select(['name'=>'panel', 'label'=>__('layout.createForm.panel'), 'value'=>null, 'options'=>$this->panelLocations()]));
        $form->add(FormElement::select(['name'=>'panelType', 'label'=>__('layout.createForm.panelType'), 'value'=>null, 'options'=>['static'=>'static', 'dynamic'=>'dynamic']]));
        
        $form->add(FormElement::html(['name'=>'panels', 'value'=>'<div id="addPanel"><a href="javascript:addPanel()"><i class="fa fa-plus"></i> ' . __('layout.createForm.addPanel') . ' </a></div><div id="layoutPanels"></div><div style="clear:left"></div>']));
        $form->add(FormElement::html(['name'=>'removePanelSpan', 'value'=>'<span class="hidden"><i class="fa fa-trash-o"></i> ' . __('layout.createForm.removePanel') . '</span>']));
        
        $form->useJSFile('/js/layouts/layoutSettingsForm.js');
        
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
    
    public function handleSettingsForm(ChangeLayoutRequest $request) {
        $this->name = $request->name;
        $this->class = $request->class;
        $this->width = $request->width;

        $createPanels = false;
        if(empty($this->id)){
            $createPanels = true;
        }
        
        $this->save();
        
        if($createPanels){
            $panels = [];
            foreach($request->all() as $key=>$val){
                preg_match('/panel_(\d+)/', $key, $match);
                if(!empty($match)){
                    $panels[$val] = $request->{"panelType_".$match[1]};
                }
            }
            
            $orderNo = 1;
            foreach ($panels as $location => $type){
                $panel = new Panel;
                
                $panel->location = $location;
                $panel->layout_id = $this->id;
                $panel->type = $type;
                $panel->orderNo = $orderNo++;
                
                $panel->save();
                
                if(!file_exists(resource_path('views/'.$request->name.'/panels/'.$location.'.blade.php'))){
                    if(!file_exists(resource_path('views/'.$request->name.'/panels/'))){
                        mkdir(resource_path('views/'.$request->name.'/panels/'), 0775, true);
                    }
                    
                    if($type == 'static'){
                        copy(resource_path('views/Just/panels/header.blade.php'), resource_path('views/'.$request->name.'/panels/'.$location.'.blade.php'));
                    }
                    else{
                        copy(resource_path('views/Just/panels/content.blade.php'), resource_path('views/'.$request->name.'/panels/'.$location.'.blade.php'));
                    }
                }
            }
        }
    }
    
    private function panelLocations() {
        $locations = [];
        
        foreach (DB::table('panelLocations')->get() as $loc){
            $locations[$loc->location] = $loc->location;
        }
        
        return $locations;
    }
    
    public static function setDefaultForm(){
        $form = new Form('/admin/settings/layout/default/set');
        
        $form->add(FormElement::select(['name'=>'layout', 'label'=>__('layout.setDefaultForm.defaultLayout'), 'value'=>Theme::active()->name ?? 'Just', 'options'=>Theme::all()->pluck('name', 'name')]));
        $form->add(FormElement::checkbox(['name'=>'change_all', 'label'=>__('layout.setDefaultForm.forAllPages')]));
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
}
