<?php

namespace Just\Models\Blocks\AddOns;

use Intervention\Image\ImageManagerStatic;
use Just\Contracts\BlockItem;
use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Models\AddOn;

/**
 * @mixin IdeHelperImage
 */
class Image extends AbstractAddOn
{
    protected $table = 'images';

    protected $fillable = ['add_on_id', 'value'];

    /**
     * Update existing block form and add new elements
     *
     * @param BlockItem $blockItem
     * @return Form
     */
    public function updateForm(BlockItem $blockItem): Form {
        $image = $this->value ?? null;

        $blockItem->form()->add(FormElement::file(['name'=>$this->addon->name."_".$this->addon->id, 'label'=>$this->addon->title]));
        if(!is_null($image)){
            $modelTable = $this->getRelations()['pivot']->pivotParent->getTable();
            $blockItem->form()->add(FormElement::html(['name'=>'addonImagePreview'.'_'.$this->addon->id, 'value'=>'<img src="/storage/'.$modelTable.'/'.$image.'_3.png" />']));
        }

        return $blockItem->form();
    }

    public function validationRules(AddOn $addon): array {
        return [
            $addon->name."_".$addon->id => "mimetypes:image/jpeg,image/png",
        ];
    }

    /**
     * Handle addon values in the existing block form
     *
     * @param ValidateRequest $request
     * @param BlockItem $blockItem
     */
    public function handleForm(ValidateRequest $request, BlockItem $blockItem) {
        if(!is_null($request->file($this->addon->name.'_'.$this->addon->id))){
            if(!file_exists(public_path('storage/'.$blockItem->getTable()))){
                mkdir(public_path('storage/'.$blockItem->getTable()), 0775);
            }

            $blockItem->deleteImage($this->value);

            $imageFile = ImageManagerStatic::make($request->file($this->addon->name.'_'.$this->addon->id));

            $fileName = uniqid();

            $imageFile->encode('png')->save(public_path('storage/'.$blockItem->getTable().'/'.$fileName.".png"));

            $blockItem->multiplicateImage($fileName);

            $request->{$this->addon->name.'_'.$this->addon->id} = $fileName;
            parent::handleForm($request, $blockItem);
        }
    }
}
