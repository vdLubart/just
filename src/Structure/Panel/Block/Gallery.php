<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Tools\Useful;
use Lubart\Form\Form;
use Lubart\Form\FormElement;

class Gallery extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption', 'description', 'image', 'block_id'
    ];
    
    protected $table = 'photos';
    
    protected $settingsTitle = 'Image';
    
    protected $neededParameters = [];
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->includeAddons();
        
        $this->form->add(FormElement::html(['value'=>'<div id="imageUploader"></div>', 'name'=>"imageUploader"]));
        
        if(!is_null($this->id)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->block_id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->block_id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }
            
            if(empty($this->parameter('ignoreCaption'))){
                $this->form->add(FormElement::text(['name'=>'caption', 'label'=>'Caption', 'value'=>$this->caption]));
            }
            if(empty($this->parameter('ignoreDescription'))){
                $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>'Description', 'value'=>$this->description]));
            }
            $this->form->add(FormElement::submit(['value'=>'Update image', 'name'=>'startUpload']));
        }
        else{
            $this->form->add(FormElement::button(['value'=>'Upload images', 'name'=>'startUpload']));
        }
        
        $this->form->setType('settings');
        $this->form->useJSFile('/js/blocks/gallery/settingsForm.js');
        
        return $this->form;
    }
    
    public function addSetupFormElements(Form &$form) {
        $this->addCropSetupGroup($form);
        
        if(\Auth::user()->role == "master"){
            $this->addIgnoretCaptionSetupGroup($form);

            $this->addResizePhotoSetupGroup($form);
        }
                
        $form->useJSFile('/js/blocks/setupForm.js');
        
        return $form;
    }
    
    public function handleForm(Request $request) {
        $parameters = json_decode($this->block()->parameters);
        $photo = null;
        
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }
        
        if (isset($request->currentFile) and is_file(public_path('storage/'.$this->table . '/' . $request->currentFile))) {
            $image = Image::make(public_path('storage/'.$this->table ."/" . $request->currentFile));
            
            if (is_null($request->get('id'))) {
                $photo = new Gallery;
                $photo->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
                $photo->setBlock($request->get('block_id'));
                $photo->image = uniqid();
            } else {
                $photo = Gallery::findOrNew($request->get('id'));
            }
            
            $photo->caption = empty($request->caption)?'':$request->caption;
            $photo->description = empty($request->description)?'':$request->description;
            
            unlink((public_path('storage/'.$this->table . '/' . $request->currentFile)));
            $image->encode('png')->save(public_path('storage/'. $this->table .'/' . $photo->image . ".png"));
            $image->destroy();
            
            $photo->save();
            
            if (isset($parameters->cropPhoto) and $parameters->cropPhoto) {
                $photo->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($photo->image);
            }
            
            Useful::normalizeOrder($this->table);
        }
        elseif(!isset($request->currentFile) and !is_null($request->get('id'))){
            $photo = Gallery::findOrNew($request->get('id'));
                
            $photo->caption = empty($request->caption)?'':$request->caption;
            $photo->description = empty($request->description)?'':$request->description;
            
            $photo->save();
        }
        
        $this->handleAddons($request, $photo);

        return $photo;
    }
}
