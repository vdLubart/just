<?php

namespace Lubart\Just\Structure;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Layout extends Model
{
    protected $table = 'layouts';
    
    protected $fillable = ['name', 'type', 'width', 'isActive'];
    
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
        $form->add(FormElement::text(['name'=>'name', 'label'=>'Theme Title', 'value'=>$this->name ?: '']));
        $form->add(FormElement::text(['name'=>'class', 'label'=>'Theme Class', 'value'=>$this->class ?: 'primary']));
        $form->add(FormElement::number(['name'=>'width', 'label'=>'Layout Width', 'value'=>$this->width ?: '1920']));
        
        $form->add(FormElement::select(['name'=>'panel', 'label'=>'Panel', 'value'=>null, 'options'=>$this->panelLocations()]));
        $form->add(FormElement::select(['name'=>'panelType', 'label'=>'Panel Type', 'value'=>null, 'options'=>['static'=>'static', 'dynamic'=>'dynamic']]));
        
        $form->add(FormElement::html(['name'=>'panels', 'value'=>'<div id="addPanel"><a href="javascript:addPanel()"><i class="fa fa-plus"></i> Add panel</a></div><div id="layoutPanels"></div><div style="clear:left"></div>']));
        
        $form->useJSLogic();
        
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function handleSettingsForm(Request $request) {
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
}
