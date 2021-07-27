<?php

namespace Just\Models\Blocks;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Just\Tools\Useful;
use Just\Models\System\Icon;
use Just\Models\System\IconSet;
use Illuminate\Http\Request;
use Just\Models\System\Route as JustRoute;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperFeatures
 */
class Features extends AbstractItem
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'icon_id', 'title', 'description', 'link', 'orderNo', 'isActive'
    ];

    public $translatable = ['title', 'description'];

    protected $table = 'features';

    public function setup() {
        if(!Useful::isRouteExists("iconset/{id}/{page?}")){
            JustRoute::create([
                'route' => "iconset/{id}/{page?}",
                'type' => 'ajax',
                'block_id' => $this->block_id,
                'action' => 'iconset'
            ]);
        }
    }

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        /**
         * @var Icon $currentIcon
         */
        $currentIcon = Icon::find($this->icon_id) ?? new Icon();
        $currentIcon->icon_set = $currentIcon->iconSet;

        $iconGroup = new FormGroup('iconGroup', __('features.form.icon'), ['class'=>'fullWidth']);

        $iconGroup->add(FormElement::html(['name'=>'icon', 'value'=>'', 'label'=>'Choose icon', 'vueComponent'=>'icon-set', 'vueComponentAttrs'=>['bundles'=>IconSet::getList(), 'value'=>$currentIcon]]));

        $this->form->addGroup($iconGroup);

        $titleGroup = new FormGroup('titleGroup', __('settings.common.description'), ['class'=>'fullWidth twoColumns']);

        if(!$this->parameter('ignoreCaption', true)){
            $titleGroup->add(FormElement::text(['name'=>'title', 'label'=>__('settings.common.title'), 'value'=>$this->getTranslations('title'), 'translate'=>true])
                ->obligatory()
            );
        }

        $titleGroup->add(FormElement::text(['name'=>'link', 'label'=>__('features.form.link'), 'value'=>$this->link]));

        $this->form->addGroup($titleGroup);

        if(!$this->parameter('ignoreDescription', true)){
            $this->form->add(FormElement::textarea(['name'=>'description', 'label'=>__('settings.common.description'), 'value'=>$this->getTranslations('description'), 'translate'=>true]));
        }

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function addCustomizationFormElements(Form &$form): Form {
        if(\Auth::user()->role == "master"){
            $this->addIgnoreCaptionSetupGroup($form);
        }

        return $form;
    }

    public function handleItemForm(ValidateRequest $request) {
        if(is_null($request->id)){
            $feature = new Features;
            $feature->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->block_id]);
        }
        else{
            $feature = Features::findOrNew($request->id);
        }
        $feature->setBlock($request->block_id);

        $feature->icon_id = $request->icon;
        $feature->title = $request->title;
        $feature->description = $request->description;
        $feature->link = $request->link;
        $feature->save();

        $this->handleAddons($request, $feature);

        return $feature;
    }

    /**
     * Get feature icon
     *
     * @return Builder
     */
    public function icon() {
        return $this->belongsTo(Icon::class);
    }

    /**
     * Build icon list in AJAX request
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function iconset(Request $request) {
        return Useful::paginate(Icon::with('iconSet')->where('icons.icon_set_id', $request->id)->get(), 76, $request->page);
    }

    /**
     * @return string
     */
    public function itemIcon():string {
        /**
         * @var Icon $icon
         */
        $icon = Icon::find($this->icon_id);
        return '<' . $icon->iconSet->tag . ' class="' .$icon->iconSet->class. ' ' . $icon->class . '"></' . $icon->iconSet->tag . '>';
    }
}
