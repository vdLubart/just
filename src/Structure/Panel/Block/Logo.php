<?php

namespace Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Form\Form;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Spatie\Translatable\HasTranslations;

class Logo extends AbstractBlock
{

    use HasTranslations;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image', 'caption', 'description'
    ];

    public $translatable = ['caption', 'description'];
    
    protected $table = 'logos';
    
    public function form() {
        if(is_null($this->form)){
            return;
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
                $this->form->add(FormElement::text(['name'=>'caption', 'label'=>__('settings.common.caption'), 'value'=>$this->caption]));
            }
            if(empty($this->parameter('ignoreDescription'))){
                $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->description]));
            }
            $this->form->add(FormElement::submit(['value'=>__('gallery.form.update'), 'name'=>'startUpload']));
        }
        else{
            $this->form->add(FormElement::button(['value'=>__('gallery.form.upload'), 'name'=>'startUpload']));
        }
        
        $this->form->setType('settings');
        $this->form->useJSFile('/js/blocks/logo/settingsForm.js');
        
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
    
    public function handleForm(ValidateRequest $request) {
        $parameters = $this->block->parameters;
        $logo = null;
        
        if (isset($request->currentFile) and is_file(public_path('storage/'.$this->table . '/' . $request->currentFile))) {
            $image = Image::make(public_path('storage/'.$this->table ."/" . $request->currentFile));
            
            if (is_null($request->get('id'))) {
                $logo = new Logo;
                $logo->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
                $logo->setBlock($request->get('block_id'));
            } else {
                $logo = Logo::findOrNew($request->get('id'));
            }
            
            $logo->image = uniqid();
            $logo->caption = empty($request->caption)?'':$request->caption;
            $logo->description = empty($request->description)?'':$request->description;
            
            unlink((public_path('storage/'.$this->table . '/' . $request->currentFile)));
            $image->encode('png')->save(public_path('storage/'. $this->table .'/' . $logo->image . ".png"));
            
            $logo->save();
            
            if (isset($parameters->cropPhoto) and $parameters->cropPhoto) {
                $logo->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($logo->image);
            }
            
            Useful::normalizeOrder($this->table);
        }
        elseif(!isset($request->currentFile) and !is_null($request->get('id'))){
            $logo = Logo::findOrNew($request->get('id'));
                
            $logo->caption = empty($request->caption)?'':$request->caption;
            $logo->description = empty($request->description)?'':$request->description;
            
            $logo->save();
        }
        
        $this->handleAddons($request, $logo);

        return $logo;
    }
}
