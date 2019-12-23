<?php

namespace Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Just\Models\Route as JustRoute;
use Just\Structure\Page;
use Just\Tools\Slug;
use Spatie\Translatable\HasTranslations;

class Articles extends AbstractBlock
{
   use Slug;
   use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'summary', 'text', 'slug', 'image'
    ];
    
    protected $table = 'articles';

    public $translatable = ['subject', 'summary', 'text'];

    protected $neededParameters = [ 'itemRouteBase' ];

    public function setup() {
        if(!empty($this->block->parameter('itemRouteBase')) and !Useful::isRouteExists($this->block->parameter('itemRouteBase') . "/{id}")){
            JustRoute::where('block_id', $this->block_id)->delete();

            JustRoute::create([
                'route' => $this->block->parameter('itemRouteBase') . "/{id}",
                'type' => 'page',
                'block_id' => $this->block_id
            ]);

            Page::create([
                'title' => str_singular($this->block->title),
                'description' => '',
                'route' => $this->block->parameter('itemRouteBase') . '/{id}',
                'layout_id' => $this->block->page()->layout_id
            ]);
        }
    }

    public function form() {
        if(is_null($this->form)){
            return;
        }

        $this->form->add(FormElement::file(['name'=>'image', 'label'=>__('settings.actions.upload')]));
        if(!is_null($this->id)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }

            if(!empty($this->parameter('cropPhoto'))){
                $this->form->add(FormElement::button(['name' => 'recrop', 'value' => __('settings.actions.recrop')]));
                $this->form->getElement("recrop")->setParameters('javasript:openCropping(' . $this->block_id . ', ' . $this->id . ')', 'onclick');
            }
        }
        $this->form->add(FormElement::text(['name'=>'subject', 'label'=>__('settings.common.subject'), 'value'=>$this->subject]));
        $this->form->add(FormElement::textarea(['name'=>'summary', 'label'=>__('settings.common.summary'), 'value'=>$this->summary]));
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>__('articles.text'), 'value'=>$this->text]));

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        $this->form->applyJS("
$(document).ready(function(){
    CKEDITOR.replace('summary');
    CKEDITOR.replace('text');
});");

        $this->markObligatoryFields($this->form);

        return $this->form;
    }

    public function addSetupFormElements(Form &$form) {
        $this->addCropSetupGroup($form);

        if(\Auth::user()->role == "master"){
            $this->addResizePhotoSetupGroup($form);
        }
        $this->addItemRouteGroup($form);

        $form->useJSFile('/js/blocks/setupForm.js');

        return $form;
    }

    public function handleForm(ValidateRequest $request) {
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }

        if(!is_null($request->file('image'))){
            $image = Image::make($request->file('image'));
        }

        if(is_null($request->get('id'))){
            $article = new Articles;
            $article->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->get('block_id')]);
        }
        else{
            $article = Articles::findOrNew($request->get('id'));
        }
        $article->setBlock($request->get('block_id'));
        if(!is_null($request->file('image'))){
            $article->image = uniqid();
        }
        $article->subject = $request->get('subject');
        $article->slug = $this->createSlug($request->get('subject'));
        $article->summary = $request->get('summary');
        $article->text = $request->get('text');
        $article->save();

        $this->handleAddons($request, $article);

        if(!is_null($request->file('image'))){
            $image->encode('png')->save(public_path('storage/'.$this->table.'/'.$article->image.".png"));

            if($this->parameter('cropPhoto')) {
                $article->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($article->image);
            }
        }

        return $article;
    }
}
