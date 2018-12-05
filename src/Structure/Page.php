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
        'title',  'description', 'keywords', 'author', 'copyright', 'route', 'layout_id'
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
        $form->add(FormElement::text(['name'=>'description', 'label'=>'Meta Description', 'value'=>$this->description, 'style'=>'width:100%']));
        $form->add(FormElement::text(['name'=>'keywords', 'label'=>'Meta Keywords', 'value'=>$this->keywords, 'style'=>'width:100%']));
        $form->add(FormElement::text(['name'=>'author', 'label'=>'Meta Author', 'value'=>$this->author]));
        $form->add(FormElement::text(['name'=>'copyright', 'label'=>'Meta Copyright', 'value'=>$this->copyright]));
        $form->add(FormElement::checkbox(['name'=>'copyMeta', 'label'=>'Use current meta data everywhere on the website']));
        $form->add(FormElement::text(['name'=>'route', 'label'=>'Route', 'value'=>$this->route]));
        $form->add(FormElement::select(['name'=>'layout_id', 'label'=>'Layout', 'value'=>$this->layout_id, 'options'=>$this->layoutsArray()]));
        $form->add(FormElement::submit(['value'=>'Save']));
        
        return $form;
    }
    
    public function handleSettingsForm(Request $request) {
        $this->title = $request->title;
        $this->description = $request->description;
        $this->keywords = $request->keywords;
        $this->author = $request->author;
        $this->copyright = $request->copyright;
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
        
        if(isset($request->copyMeta)){
            Page::query()->update([
                'description' => $request->description,
                'keywords' => $request->keywords,
                'author' => $request->author,
                'copyright' => $request->copyright,
            ]);
        }
    }
    
    private function layoutsArray() {
        $layouts = [];
        
        foreach(Layout::all() as $layout){
            $layouts[$layout->id] = $layout->name . (!empty($layout->class)? ".".$layout->class : "");
        }
        
        return $layouts;
    }
    
    public static function setLayoutToAllPages($layout){
        Page::where('id', '>', 0)->update(['layout_id'=>$layout->id]);
    }
}
