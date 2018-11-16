<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Lubart\Just\Requests\ArticleChangeRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Tools\Useful;
use Illuminate\Http\Request;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Just\Structure\Page;
use Lubart\Just\Structure\Panel;

class Articles extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'summary', 'text'
    ];
    
    protected $table = 'articles';
    
    /**
     * Title for model
     * 
     * @var string $settingsTitle
     */
    protected $settingsTitle = 'Article';
    
    protected $neededParameters = [
        'imageWidth'     => 'Article image width',
        'imageHeight'    => 'Article image height',
    ];
    
    /**
     * Return block content
     * 
     * @param int $id
     * @return Article
     */
    public function content($id = null) {
        if(is_null($id)){
            $content = $this->orderBy('orderNo')
                    ->where('block_id', $this->block_id);
            if(!\Config::get('isAdmin')){
                $content = $content->where('isActive', 1);
            }
            
            return $content->get();
        }
        else{
            return $this->find($id);
        }
    }
    
    public function setup() {
        if(!Useful::isRouteExists("article/{id}")){
            JustRoute::create([
                'route' => "article/{id}",
                'type' => 'page',
                'block_id' => $this->block_id,
                'action' => 'article'
            ]);
            
            Page::create([
                'title' => 'Article',
                'description' => '',
                'route' => 'article/{id}',
                'layout_id' => $this->block()->page->layout_id
            ]);
        }
    }
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->form->add(FormElement::file(['name'=>'image', 'label'=>'Upload Image']));
        if(!is_null($this->id)){
            $this->form->add(FormElement::button(['name'=>'recrop', 'value'=>'Recrop Image']));
            $this->form->getElement("recrop")->setParameters('javasript:openCropping('.$this->block_id.', '.$this->id.')', 'onclick');
        }
        $this->form->add(FormElement::text(['name'=>'subject', 'label'=>'Subject', 'value'=>$this->subject]));
        $this->form->add(FormElement::textarea(['name'=>'summary', 'label'=>'Summary', 'value'=>$this->summary]));
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>'Article Text', 'value'=>$this->text]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        /*
        $this->form->applyJS("
$(document).ready(function(){
    CKEDITOR.replace('summary');
    CKEDITOR.replace('text');
});");*/
        $this->form->useJSFile('/js/blocks/'.$this->block()->name.'/settingsForm.js');
        
        return $this->form;
    }
    
    public function handleForm(ArticleChangeRequest $request) {
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
        $article->summary = $request->get('summary');
        $article->text = $request->get('text');
        $article->save();
        
        $this->handleAddons($request, $article);
        
        if(!is_null($request->file('image'))){
            $image->encode('png')->save(public_path('storage/'.$this->table.'/'.$article->image.".png"));

            $article->shouldBeCropped = true;
        }
        
        return $article;
    }
    
    /**
     * Get specific article data
     * 
     * @param Request $request
     * @return Articles
     */
    public function article(Request $request) {
        return $this->content($request->id);
    }
}
