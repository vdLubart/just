<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\FormElement;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Form\Form;
use Lubart\Just\Tools\Useful;

class Logo extends AbstractBlock
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image', 'caption', 'description'
    ];
    
    protected $table = 'logos';
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->includeAddons();
        
        $this->form->add(FormElement::html(['value'=>'<div id="imageUploader"></div>', 'name'=>"imageUploader"]));
        
        if(!is_null($this->id)){
            $this->form->add(FormElement::text(['name'=>'caption', 'value'=>$this->caption]));
            $this->form->add(FormElement::textarea(['name'=>'description', 'value'=>$this->description]));
            $this->form->add(FormElement::submit(['value'=>'Update image', 'name'=>'startUpload']));
        }
        else{
            $this->form->add(FormElement::button(['value'=>'Upload images', 'name'=>'startUpload']));
        }
        
        $this->form->setType('settings');
        $this->form->useJSLogic();
        
        return $this->form;
    }
    
    public function setupForm(Block $block) {
        $parameters = json_decode($block->parameters);
        
        $form = new Form('/admin/settings/setup');
        
        $form->setType('setup');
        
        $form->add(FormElement::hidden(['name'=>'id', 'value'=>$block->id]));
        
        $form->add(FormElement::checkbox(['name'=>'cropPhoto', 'label'=>'Crop photo', 'value'=>1, 'check'=>(@$parameters->cropPhoto==1)]));
        $form->add(FormElement::text(['name'=>'cropDimentions', 'label'=>'Crop image with dimentions (W:H)', 'value'=>isset($parameters->cropDimentions)?$parameters->cropDimentions:'4:3']));
        
        foreach($this->customAttributes() as $attr){
            $form->add(FormElement::text(['name'=>$attr->name, 'label'=>$attr->label, 'value'=>isset($parameters->{$attr->name})?$parameters->{$attr->name}:$attr->defaultValue]));
        }
        
        $form->add(FormElement::submit(['value'=>'Save']));
        
        $form->useJSLogic();
        
        return $form;
    }
    
    public function handleForm(Request $request) {
        $parameters = json_decode($this->block()->parameters);
        $logo = null;
        
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }
        
        if (isset($request->currentFile) and is_file(public_path('storage/'.$this->table . '/' . $request->currentFile))) {
            $image = Image::make(public_path('storage/'.$this->table ."/" . $request->currentFile));
            
            if (is_null($request->get('id'))) {
                $logo = new Logo;
                $logo->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
                $logo->setBlock($request->get('block_id'));
                $logo->image = uniqid();
            } else {
                $logo = Logo::findOrNew($request->get('id'));
            }
            
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
