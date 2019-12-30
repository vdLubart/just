<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Just\Models\System\Route as JustRoute;
use Just\Requests\ChangePageRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Illuminate\Http\Request;
use Spatie\Translatable\HasTranslations;
use Lubart\Form\FormGroup;

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
     * Return Route model for current page
     *
     * @return mixed
     */
    public function getRoute() {
        return JustRoute::where('route', $this->route)->first();
    }
    
    /**
     * Get page settings form
     * 
     * @return Form
     * @throws \Exception
     */
    public function settingsForm() {
        $form = new Form('/settings/page/setup');

        $pageSettings = new FormGroup('pageSettings', 'Page Settings');

        $pageSettings->add(FormElement::hidden(['name'=>'page_id', 'value'=>(string)$this->id]));

        $pageSettings->add(FormElement::text(['name' => 'title', 'label' => __('page.createForm.pageTitle'), 'value' => $this->getTranslations('title'), 'translate' => true]));
        $pageSettings->add(FormElement::text(['name'=>'route', 'label'=>__('page.createForm.route'), 'value'=>"/" . $this->route]));
        $pageSettings->add(FormElement::select(['name'=>'layout', 'label'=>__('page.createForm.layout'), 'value'=>(string)$this->layout_id, 'options'=>$this->layoutsArray()])
            ->obligatory()
        );

        $form->addGroup($pageSettings);

        $metaData = new FormGroup('metaData', 'Meta Data');

        $metaData->add(FormElement::text(['name'=>'description', 'label'=>__('page.createForm.metaDescription'), 'value'=>$this->getTranslations('description'), 'translate' => true]));
        $metaData->add(FormElement::text(['name'=>'keywords', 'label'=>__('page.createForm.metaKeywords'), 'value'=>$this->keywords]));
        $metaData->add(FormElement::text(['name'=>'author', 'label'=>__('page.createForm.metaAuthor'), 'value'=>$this->getTranslations('author'), 'translate' => true]));
        $metaData->add(FormElement::text(['name'=>'copyright', 'label'=>__('page.createForm.metaCopyright'), 'value'=>$this->getTranslations('copyright'), 'translate' => true]));
        $metaData->add(FormElement::checkbox(['name'=>'copyMeta', 'label'=>__('page.createForm.copyMeta')]));

        $form->addGroup($metaData);

        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
    
    public function handleSettingsForm(ChangePageRequest $request) {
        $this->title = $request->title ?? '';
        $this->description = $request->description ?? '';
        $this->keywords = $request->keywords ?? '';
        $this->author = $request->author ?? '';
        $this->copyright = $request->copyright ?? '';
        $this->layout_id = $request->layout;
        
        if(empty($this->id)){
            JustRoute::insert([
                'route' => $request->route,
                'type' => 'page'
            ]);
            
            $this->route = $request->route;
        }
        else{
            JustRoute::where('route', $this->route)->first()
                    ->update([
                        'route' => $request->route
                    ]);
        }
        
        $this->save();

        if(isset($request->copyMeta)){
            Page::query()->update([
                'description' => json_encode([\App::getLocale() =>$request->description ?? '']),
                'keywords' => $request->keywords ?? '',
                'author' => json_encode([\App::getLocale() => $request->author ?? '']),
                'copyright' => json_encode([\App::getLocale() => $request->copyright ?? '']),
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

    /**
     * Apply layout to all existing pages
     *
     * @param Layout $layout
     */
    public static function setLayoutToAllPages(Layout $layout){
        Page::where('id', '>', 0)->update(['layout_id'=>$layout->id]);
    }

    public static function current() {
        $currentUri = trim(Route::getFacadeRoot()->current()->uri(), "/");

        return JustRoute::findByUrl($currentUri)->page;
    }
}
