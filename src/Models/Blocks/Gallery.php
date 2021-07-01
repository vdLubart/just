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
            $this->form->add(FormElement::html(['name'=>'externalUrl', 'value'=>'', 'label'=>'Upload items', "ref"=>"uploader", 'vueComponent'=>'create-gallery-item', 'vueComponentAttrs'=>[
                "token"=>csrf_token(),
                "additionalParameters" => ['block_id' => $this->block_id]
            ]]));
        }
        else{
            if(!empty($this->image)){
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

                $this->form->add(FormElement::file(['name'=>'image', 'label'=>__('settings.gallery.form.update')]));
            }
            elseif(!empty($this->video)){
                $this->form->add(FormElement::file(['name'=>'video', 'label'=>__('settings.gallery.form.updateVideo')]));
            }
            elseif(!empty($this->externalUrl)){
                $this->form->add(FormElement::text(['name'=>'externalUrl', 'label'=>__('settings.gallery.form.externalUrl'), 'value'=>$this->externalUrl]));
            }

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
        $photo = new Gallery;

        if (isset($request->image)) {
            $this->createUploadDirectory();

            $image = Image::make($request->image);

            $photo = $this->detectPhoto($request);

            $photo->image = uniqid();
            $photo->video = null;
            $photo->externalUrl = null;
            $photo->caption = $request->caption ?? '';
            $photo->description = $request->description ?? '';

            $image->encode('png')->save(public_path('storage/'. $this->table .'/' . $photo->image . ".png"));
            $image->destroy();

            $photo->save();

            $photo->shouldBeCropped = !! ($parameters->cropPhoto ?? false);

            $this->multiplicateImage($photo->image);

            Useful::normalizeOrder($this->table);
        }
        elseif (isset($request->video)) {
            $this->createUploadDirectory();

            $photo = $this->detectPhoto($request);

            $photo->image = null;
            $photo->video = uniqid();
            $photo->externalUrl = null;
            move_uploaded_file($request->video->getRealPath(), public_path('storage/'. $this->table .'/' . $photo->video . ".mp4"));
            $photo->caption = $request->caption ?? '';
            $photo->description = $request->description ?? '';

            $photo->save();

            $photo->shouldBeCropped = false;

            Useful::normalizeOrder($this->table);
        }
        elseif (!empty($request->externalUrl)) {
            $photo = $this->detectPhoto($request);

            $photo->image = null;
            $photo->video = null;
            $photo->externalUrl = $request->externalUrl;
            $photo->caption = $request->caption ?? '';
            $photo->description = $request->description ?? '';

            $photo->save();

            $photo->shouldBeCropped = false;

            Useful::normalizeOrder($this->table);
        }
        elseif(!is_null($request->id)){
            $photo = Gallery::findOrNew($request->id);

            $photo->caption = $request->caption ?? '';
            $photo->description = $request->description ?? '';

            $photo->save();
        }

        $this->handleAddons($request, $photo);

        return $photo;
    }

    /**
     * Create new photo instance or find existing if the ID presents in the request
     *
     * @param ValidateRequest $request
     * @return Gallery
     */
    private function detectPhoto(ValidateRequest $request): Gallery {
        if (is_null($request->id)) {
            $photo = new Gallery;
            $photo->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->block_id]);
            $photo->setBlock($request->block_id);
        } else {
            $photo = Gallery::findOrNew($request->id);
            $this->deleteImage($photo->image);
        }

        return $photo;
    }

    public function itemImage(): ?string {
        return $this->image ? $this->imageSource(3) : null;
    }

    public function itemCaption():string {
        return ($this->video ? '[Video File] ' : ($this->externalUrl ? '['.$this->externalUrl.']' : '') ) . $this->caption;
    }

    public function itemText():string {
        return substr($this->description, 100);
    }

}
