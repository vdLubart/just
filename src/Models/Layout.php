<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Support\Facades\DB;
use Lubart\Form\FormGroup;
use Just\Requests\ChangeLayoutRequest;
use Exception;

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
     * @throws Exception;
     */
    public function settingsForm() {
        $form = new Form('/settings/layout/setup');
        
        $paramsGroup = new FormGroup('paramsGroup', 'Layout Parameters');

        $paramsGroup->add(FormElement::hidden(['name'=>'layout_id', 'value'=>(string) $this->id]));
        $paramsGroup->add(FormElement::select(['name'=>'name', 'label'=>__('layout.createForm.themeTitle'), 'value'=>$this->name ?? Theme::where('isActive', 1)->first()->name, 'options'=>Theme::all()->pluck('name', 'name')])
            ->obligatory()
        );
        $paramsGroup->add(FormElement::text(['name'=>'class', 'label'=>__('layout.createForm.themeClass'), 'value'=>$this->class ?? 'primary'])
            ->obligatory()
        );
        if($this->id == 1){
            $paramsGroup->element("name")->setParameter("disabled", "disabled");
            $paramsGroup->element("class")->setParameter("disabled", "disabled");
        }
        $paramsGroup->add(FormElement::number(['name'=>'width', 'label'=>__('layout.createForm.width'), 'value'=>(int)($this->width ?: '1920')])
            ->obligatory()
        );

        $form->addGroup($paramsGroup);

        $defaultGroup = new FormGroup('defaultGroup', 'Default Layout');

        $defaultGroup->add(FormElement::checkbox(['name'=>'isDefault', 'label'=>__('layout.setDefaultForm.defaultLayout'), 'value'=>Theme::active()->name == $this->name]));
        $defaultGroup->add(FormElement::checkbox(['name'=>'applyOnAllPages', 'label'=>__('layout.setDefaultForm.forAllPages')]));

        $form->addGroup($defaultGroup);

        $panelGroup = new FormGroup('panelGroup', 'Layout Panel');

        $panelGroup->add(FormElement::select(['name'=>'panel', 'label'=>__('layout.createForm.panel'), 'value'=>null, 'options'=>$this->panelLocations()]));
        $panelGroup->add(FormElement::select(['name'=>'panelType', 'label'=>__('layout.createForm.panelType'), 'value'=>null, 'options'=>['static'=>'static', 'dynamic'=>'dynamic']]));

        $panelGroup->add(FormElement::html(['name'=>'addPanel', 'value'=>'<div id="addPanel"><a href="javascript:CreateLayout.addPanel()"><i class="fa fa-plus"></i> ' . __('layout.createForm.addPanel') . ' </a></div>']));
        $panelGroup->add(FormElement::html(['name'=>'removePanel', 'value'=>'<div id="removePanel"><a href="#" onclick="CreateLayout.removePanel(this)"><i class="fa fa-trash-alt"></i> ' . __('layout.createForm.removePanel') . '</a></div>']));

        $form->addGroup($panelGroup);
        
        $form->applyJS("window.CreateLayout = this.getClassInstance('CreateLayout')");

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
                $panel = new Panel();
                
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

        if(isset($request->isDefault) and !!$request->isDefault) {
            Theme::setActive($this->name);
        }

        if(isset($request->applyOnAllPages)){
            Page::setLayoutToAllPages($this);
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
        $form = new Form('settings/layout/setdefault');
        
        $form->add(FormElement::select(['name'=>'layout', 'label'=>__('layout.setDefaultForm.defaultLayout'), 'value'=>Theme::active()->name ?? 'Just', 'options'=>Theme::all()->pluck('name', 'name')]));
        $form->add(FormElement::checkbox(['name'=>'change_all', 'label'=>__('layout.setDefaultForm.forAllPages')]));
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
}
