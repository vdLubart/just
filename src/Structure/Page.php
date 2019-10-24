<?php

namespace Lubart\Just\Structure;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',  'description', 'keywords', 'author', 'copyright', 'route', 'layout_id'
    ];

    public $translatable = ['title', 'description', 'author', 'copyright'];
    
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
        $form->add(FormElement::text(['name'=>'title', 'label'=>__('page.createForm.pageTitle'), 'value'=>$this->title]));
        $form->add(FormElement::text(['name'=>'description', 'label'=>__('page.createForm.metaDescription'), 'value'=>$this->description, 'style'=>'width:100%']));
        $form->add(FormElement::text(['name'=>'keywords', 'label'=>__('page.createForm.metaKeywords'), 'value'=>$this->keywords, 'style'=>'width:100%']));
        $form->add(FormElement::text(['name'=>'author', 'label'=>__('page.createForm.metaAuthor'), 'value'=>$this->author]));
        $form->add(FormElement::text(['name'=>'copyright', 'label'=>__('page.createForm.metaCopyright'), 'value'=>$this->copyright]));
        $form->add(FormElement::checkbox(['name'=>'copyMeta', 'label'=>__('page.createForm.copyMeta')]));
        $form->add(FormElement::text(['name'=>'route', 'label'=>__('page.createForm.route'), 'value'=>$this->route]));
        $form->add(FormElement::select(['name'=>'layout_id', 'label'=>__('page.createForm.layout'), 'value'=>$this->layout_id, 'options'=>$this->layoutsArray()]));
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
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
                'description' => json_encode([\App::getLocale() =>$request->description]),
                'keywords' => $request->keywords,
                'author' => json_encode([\App::getLocale() => $request->author]),
                'copyright' => json_encode([\App::getLocale() => $request->copyright]),
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

    public static function current() {
        $currentUri = trim(Route::getFacadeRoot()->current()->uri(), "/");

        return JustRoute::findByUrl($currentUri)->page;
    }
}
