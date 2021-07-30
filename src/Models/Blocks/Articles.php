<?php

namespace Just\Models\Blocks;

use Exception;
use Illuminate\Support\Facades\Auth;
use Just\Contracts\Requests\BlockItems\ValidateArticleRequest;
use Just\Contracts\Requests\ValidateRequest;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Tools\Useful;
use Just\Models\System\Route as JustRoute;
use Just\Models\Page;
use Just\Tools\Slug;
use Spatie\Translatable\HasTranslations;
use Lubart\Form\FormGroup;

/**
 * @property string shouldBeCropped
 * @mixin IdeHelperArticles
 */
class Articles extends AbstractItem
{
   use Slug, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'summary', 'text', 'slug', 'image'
    ];

    protected $table = 'articles';

    public array $translatable = ['subject', 'summary', 'text'];

    protected array $neededParameters = [ 'itemRouteBase' ];

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        $imageGroup = new FormGroup('imageGroup', __('articles.imageGroup.title'), ['class'=>'twoColumns']);

        if(!is_null($this->id)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $imageGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $imageGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }

            if(!empty($this->parameter('cropPhoto'))){
                $imageGroup->add(FormElement::button(['name' => 'recrop', 'value' => __('settings.actions.recrop')]));
                $imageGroup->element("recrop")->setParameter('window.App.navigate(\'/settings/block/'.$this->block_id.'/item/'.$this->id.'/cropping\')', 'onclick');
            }
        }

        $imageGroup->add($imageField = FormElement::file(['name'=>'image', 'label'=>__('settings.actions.upload')]));

        if(empty($this->id)){
            $imageField->obligatory();
        }

        $this->form->addGroup($imageGroup);

        $textGroup = new FormGroup('textGroup', __('articles.textGroup.title'), ['class'=>'fullWidth']);

        $textGroup->add(FormElement::text(['name'=>'subject', 'label'=>__('settings.common.subject'), 'value'=>$this->getTranslations('subject'), 'translate'=>true])
            ->obligatory()
        );
        $textGroup->add(FormElement::textarea(['name'=>'summary', 'label'=>__('settings.common.summary'), 'value'=>$this->getTranslations('summary'), 'translate'=>true]));
        $textGroup->add(FormElement::textarea(['name'=>'text', 'label'=>__('articles.text'), 'value'=>$this->getTranslations('text'), 'translate'=>true])
            ->obligatory()
        );

        $this->form->addGroup($textGroup);

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        $this->markObligatoryFields($this->form);

        return $this->form;
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function addCustomizationFormElements(Form &$form): Form {
        $this->addCropSetupGroup($form);

        if(Auth::user()->role == "master"){
            $this->addResizePhotoSetupGroup($form);
        }
        $this->addItemRouteGroup($form);

        return $form;
    }

    /**
     * @throws Exception
     */
    public function handleItemForm(ValidateArticleRequest $request) {
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }

        if(is_null($request->id)){
            $article = new Articles;
            $article->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->block_id]);
        }
        else{
            $article = Articles::findOrNew($request->id);
        }
        $article->setBlock($request->block_id);
        if(!is_null($request->file('image'))){
            $this->deleteImage($article->image);
            $article->image = uniqid();
        }

        $article->subject = $request->subject;
        $article->slug = $this->createSlug($request->subject['en']);
        $article->summary = $request->summary;
        $article->text = $request->text;
        $article->save();

        $this->handleAddons($request, $article);

        if(!is_null($request->file('image'))){
            $image = Image::make($request->file('image'));
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

    public function itemCaption(): string {
        return $this->subject;
    }

    public function itemImage():string {
        return $this->imageSource(3);
    }
}
