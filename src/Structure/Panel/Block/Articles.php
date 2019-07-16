<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Requests\ChangeArticleRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Just\Structure\Page;
use Lubart\Just\Tools\Slug;

class Articles extends AbstractBlock
{
   use Slug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'slug', 'summary', 'text', 'image'
    ];
    
    protected $table = 'articles';

    protected $neededParameters = [ 'itemRouteBase' ];

    protected $settingsTitle = 'Article';
    
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

        $this->form->add(FormElement::file(['name'=>'image', 'label'=>'Upload Image']));
        if(!is_null($this->id)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }

            if(!empty($this->parameter('cropPhoto'))){
                $this->form->add(FormElement::button(['name' => 'recrop', 'value' => 'Recrop Image']));
                $this->form->getElement("recrop")->setParameters('javasript:openCropping(' . $this->block_id . ', ' . $this->id . ')', 'onclick');
            }
        }
        $this->form->add(FormElement::text(['name'=>'subject', 'label'=>'Subject', 'value'=>$this->subject]));
        $this->form->add(FormElement::textarea(['name'=>'summary', 'label'=>'Summary', 'value'=>$this->summary]));
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>'Article Text', 'value'=>$this->text]));

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>'Save']));

        $this->form->applyJS("
$(document).ready(function(){
    CKEDITOR.replace('summary');
    CKEDITOR.replace('text');
});");

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

    public function handleForm(ChangeArticleRequest $request) {
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

            if($this->parameter('shouldBeCropped')) {
                $article->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($article->image);
            }
        }

        return $article;
    }
}
