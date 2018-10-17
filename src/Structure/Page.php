<?php

namespace Lubart\Just\Structure;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Http\Request;

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',  'description', 'route', 'layout_id', 
    ];
    
    protected $table = 'pages';
    
    public function layout() {
        return $this->belongsTo(Layout::class);
    }
    
    /**
     * Get page settings form
     * 
     * @return Form
     */
    public function settingsForm() {
        $form = new Form('admin/settings/page/setup');
        
        $form->add(FormElement::hidden(['name'=>'page_id', 'value'=>$this->id]));
        $form->add(FormElement::text(['name'=>'title', 'label'=>'Page Title', 'value'=>$this->title]));
        $form->add(FormElement::textarea(['name'=>'description', 'label'=>'Page Description', 'value'=>$this->description]));
        $form->add(FormElement::text(['name'=>'route', 'label'=>'Route', 'value'=>$this->route]));
        $form->add(FormElement::select(['name'=>'layout_id', 'label'=>'Layout', 'value'=>$this->layout_id, 'options'=>$this->layoutsArray()]));
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function handleSettingsForm(Request $request) {
        $this->title = $request->title;
        $this->description = $request->description;
        $this->layout_id = $request->layout_id;
        
        $newRoute = is_null($request->route)?"":$request->route;
        
        if(empty($this->id)){
            \Lubart\Just\Models\Route::insert([
                'route' => $newRoute,
                'type' => 'page'
            ]);
            
            $this->route = $newRoute;
        }
        else{
            \Lubart\Just\Models\Route::where('route', $this->route)->first()
                    ->update([
                        'route' => $newRoute
                    ]);
        }
        
        $this->save();
    }
    
    private function layoutsArray() {
        $layouts = [];
        
        foreach(Layout::all() as $layout){
            $layouts[$layout->id] = $layout->name . (!empty($layout->class)? ".".$layout->class : "");
        }
        
        return $layouts;
    }
}
