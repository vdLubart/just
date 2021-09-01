<?php

namespace Just\Models\Blocks;

use Illuminate\Support\Facades\Auth;
use Lubart\Form\FormElement;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Form\Form;
use Just\Contracts\Requests\ValidateRequest;
use Just\Tools\Useful;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperLogo
 */
class Logo extends AbstractItem
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

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        if(!is_null($this->id)){
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
        }

        $this->form->add(FormElement::file(['name'=>'image', 'label'=>__('settings.actions.upload')]));

        if(empty($this->parameter('ignoreCaption'))){
            $this->form->add(FormElement::text(['name'=>'caption', 'label'=>__('settings.common.caption'), 'value'=>$this->getTranslations('caption'), 'translate'=>true]));
        }
        if(empty($this->parameter('ignoreDescription'))){
            $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->getTranslations('description'), 'translate'=>true]));
        }

        $this->includeAddons();

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
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }

        if(!is_null($request->file('image'))){
            $image = Image::make($request->file('image'));
        }

        if(is_null($request->id)){
            $logo = new Logo;
            $logo->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->block_id]);
        }
        else{
            $logo = Logo::findOrNew($request->id);
        }
        $logo->setBlock($request->block_id);
        if(!is_null($request->file('image'))){
            if(!empty($logo->image)) {
                $this->deleteImage($logo->image);
            }
            $logo->image = uniqid();
        }

        $logo->caption = $request->caption;
        $logo->description = $request->description;
        $logo->save();

        $this->handleAddons($request, $logo);

        Useful::normalizeOrder($this->table);

        if(!is_null($request->file('image'))){
            $image->encode('png')->save(public_path('storage/'.$this->table.'/'.$logo->image.".png"));

            if($this->parameter('cropPhoto')) {
                $logo->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($logo->image);
            }
        }

        return $logo;
    }

    public function itemImage(): ?string {
        return $this->imageSource(3);
    }

    public function itemCaption(): ?string {
        return $this->caption;
    }
}
