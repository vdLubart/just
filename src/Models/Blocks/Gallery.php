<?php

namespace Just\Models\Blocks;

use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Spatie\Translatable\HasTranslations;

class Gallery extends AbstractItem
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption', 'description', 'image', 'block_id'
    ];

    public $translatable = ['caption', 'description'];

    protected $table = 'photos';

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        $this->includeAddons();

        if(empty($this->id)){
            $this->form->add(FormElement::html(['name'=>'imageUploader', 'value'=>'', 'label'=>'Upload images', "ref"=>"uploader", 'vueComponent'=>'ajax-uploader', 'vueComponentAttrs'=>[
                "token"=>csrf_token(),
                "uploadUrl"=>"/settings/block/item/save",
                "allowedExtensions" => ['png', 'jpg', 'jpeg'],
                "additionalParameters" => ['block_id' => $this->block_id]
                ]]));
        }
        else{
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->block_id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $this->form->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->block_id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }
            if(!empty($this->parameter('cropPhoto'))){
                $this->form->add(FormElement::button(['name' => 'recrop', 'value' => __('settings.actions.recrop')]));
                $this->form->element("recrop")->setParameter('window.App.navigate(\'/settings/block/'.$this->block_id.'/item/'.$this->id.'/cropping\')', 'onclick');
            }

            $this->form->add(FormElement::file(['name'=>'currentFile', 'label'=>__('settings.actions.upload')]));

            if(empty($this->parameter('ignoreCaption'))){
                $this->form->add(FormElement::text(['name'=>'caption', 'label'=>__('settings.common.caption'), 'value'=>$this->getTranslations('caption'), 'translate'=>true]));
            }
            if(empty($this->parameter('ignoreDescription'))){
                $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->getTranslations('description'), 'translate'=>true]));
            }
        }

        return $this->form;
    }

    public function addCustomizationFormElements(Form &$form): Form {
        $this->addCropSetupGroup($form);

        if(Auth::user()->role == "master"){
            $this->addIgnoreCaptionSetupGroup($form);

            $this->addResizePhotoSetupGroup($form);
        }

        return $form;
    }

    public function handleItemForm(ValidateRequest $request) {
        $parameters = $this->block->parameters;

        if (isset($request->currentFile)) {
            if(!file_exists(public_path('storage/'. $this->table))){
                mkdir(public_path('storage/'. $this->table), 0775);
            }

            $image = Image::make($request->currentFile);

            if (is_null($request->get('id'))) {
                $photo = new Gallery;
                $photo->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
                $photo->setBlock($request->get('block_id'));
            } else {
                $photo = Gallery::findOrNew($request->get('id'));
                $this->deleteImage($photo->image);
            }

            $photo->image = uniqid();
            $photo->caption = $request->caption ?? '';
            $photo->description = $request->description ?? '';

            $image->encode('png')->save(public_path('storage/'. $this->table .'/' . $photo->image . ".png"));
            $image->destroy();

            $photo->save();

            if (isset($parameters->cropPhoto) and $parameters->cropPhoto) {
                $photo->shouldBeCropped = true;
            }
            else{
                $photo->shouldBeCropped = false;
            }
            $this->multiplicateImage($photo->image);

            Useful::normalizeOrder($this->table);
        }
        elseif(!isset($request->currentFile) and !is_null($request->get('id'))){
            $photo = Gallery::findOrNew($request->get('id'));

            $photo->caption = empty($request->caption)?'':$request->caption;
            $photo->description = empty($request->description)?'':$request->description;

            $photo->save();

            $photo->shouldBeCropped = false;
        }
        else{
            $photo = new Gallery;
            $photo->shouldBeCropped = false;
        }

        $this->handleAddons($request, $photo);

        return $photo;
    }

    public function itemImage():string {
        return $this->imageSource(3);
    }

    public function itemCaption():string {
        return $this->caption;
    }

    public function itemText():string {
        return substr($this->description, 100);
    }

}
