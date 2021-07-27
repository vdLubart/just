<?php

namespace Just\Models\Blocks;

use Exception;
use Just\Models\EventRegistration;
use Just\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Intervention\Image\ImageManagerStatic as Image;
use Just\Contracts\ContainsPublicForm;
use Just\Contracts\Requests\ValidateRequest;
use Just\Tools\Useful;
use Just\Models\System\Route as JustRoute;
use Just\Models\Page;
use Just\Tools\Slug;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperEvents
 */
class Events extends AbstractItem implements ContainsPublicForm
{
    use HasTranslations, Slug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'summary', 'slug', 'text', 'location', 'image', 'date_start', 'date_end'
    ];

    protected $table = 'events';

    protected $registerUrl = 'register-event';

    public $translatable = ['subject', 'summary', 'text', 'location'];

    protected array $neededParameters = [ 'itemRouteBase' ];

    /**
     * Order by `start_date` column
     *
     * @param $content
     * @param string $column
     * @return mixed
     */
    protected function orderContent(&$content, $column = 'start_date'){
        return $content->orderBy($column, $this->parameter('orderDirection') ?? 'asc');
    }

    /**
     * Show only future events
     *
     * @param $content
     * @return mixed
     */
    protected function limitContent(&$content){
        return $content->where('start_date', '>', now());
    }

    public function pastEvents(){
        $content = $this->where('block_id', $this->block->id);
        if(!\Config::get('isAdmin')){
            $content = $content->where('isActive', 1);
        }

        $curCategory = $this->block->currentCategory();
        if(!is_null($curCategory) and $curCategory->addon->block->id == $this->block->id){
            $content = $content
                ->join($this->table."_categories", $this->table."_categories.modelItem_id", "=", $this->table.".id")
                ->where("addonItem_id", $this->block->currentCategory()->id);
        }

        $content->where('start_date', '<', now());
        $content->orderBy('start_date', 'desc');

        $with = [];
        foreach($this->addons as $addon){
            $with[] = $addon->type;
        }

        $collection = $content->with($with)->get();

        foreach($collection as $item){
            $item->attachAddons();
        }

        return $collection;
    }

    public function setup() {
        if(!empty($this->block->parameter('itemRouteBase')) and !Useful::isRouteExists($this->block->parameter('itemRouteBase') . "/{id}")){
            JustRoute::where('block_id', $this->block_id)->delete();

            JustRoute::create([
                'route' => $this->block->parameter('itemRouteBase') . "/{id}",
                'type' => 'page',

                'block_id' => $this->block_id
            ]);

            Page::create([
                'title' => __('event.title'),
                'description' => '',
                'route' => $this->block->parameter('itemRouteBase') . '/{id}',
                'layout_id' => $this->block->page()->layout_id
            ]);
        }

        if(!Useful::isRouteExists("eventform/{id}")){
            JustRoute::create([
                'route' => "eventform/{id}",
                'type' => 'ajax',
                'block_id' => $this->block_id,
                'action' => 'registerForm'
            ]);

            JustRoute::create([
                'route' => $this->registerUrl,
                'type' => 'post',
                'block_id' => $this->block_id
            ]);
        }
    }

    public function neededJavaScripts(): array {
        return ["https://www.google.com/recaptcha/api.js"];
    }

    public function itemForm(): Form {
        if(is_null($this->form)){
            return new Form();
        }

        $this->identifyItemForm();

        $imageGroup = new FormGroup('topGroup', '__Event Poster', ['class'=>'fullWidth twoColumns']);

        $imageGroup->add($imageField = FormElement::file(['name'=>'image', 'label'=>__('settings.actions.upload')]));
        if(!is_null($this->id) and !empty($this->image)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $imageGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $imageGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }

            if(!empty($this->parameter('cropPhoto'))) {
                $imageGroup->add(FormElement::button(['name' => 'recrop', 'value' => __('settings.actions.recrop')]));
                $imageGroup->element("recrop")->setParameter('App.navigate(\'/settings/block/' . $this->block_id . '/item/' . $this->id . '/cropping\')', 'onclick');
            }
        }
        else{
            $imageField->obligatory();
        }

        $this->form->addGroup($imageGroup);

        $subjectGroup = new FormGroup('subjectGroup', '');

        $subjectGroup->add(FormElement::text(['name'=>'subject', 'label'=>__('settings.common.subject'), 'value'=>$this->getTranslations('subject'), 'translate'=>true])
            ->obligatory()
        );

        $this->form->addGroup($subjectGroup);

        $dateGroup = new FormGroup('startDate', '__Date and Location', ['class'=>'fullWidth twoColumns']);
        if(!empty($this->start_date)) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date);
            $dateGroup->add(FormElement::date(['name' => 'start_date', 'label' => __('events.form.startDate'), 'value' => $startDate->format('Y-m-d')])
                ->obligatory()
            );
            $dateGroup->add(FormElement::time(['name' => 'start_time', 'label' => __('events.form.startTime'), 'value' => $startDate->format('H:i')]));
        }
        else{
            $dateGroup->add(FormElement::date(['name' => 'start_date', 'label' => __('events.form.startDate')])
                ->obligatory()
            );
            $dateGroup->add(FormElement::time(['name' => 'start_time', 'label' => __('events.form.startTime')]));
        }

        if(!empty($this->end_date)) {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date);
            $dateGroup->add(FormElement::date(['name' => 'end_date', 'label' => __('events.form.endDate'), 'value' => $endDate->format('Y-m-d')]));
            $dateGroup->add(FormElement::time(['name' => 'end_time', 'label' => __('events.form.endTime'), 'value' => $endDate->format('H:i')]));
        }
        else{
            $dateGroup->add(FormElement::date(['name' => 'end_date', 'label' => __('events.form.endDate')]));
            $dateGroup->add(FormElement::time(['name' => 'end_time', 'label' => __('events.form.endTime')]));
        }

        $dateGroup->add(FormElement::text(['name'=>'location', 'label'=>__('events.form.location'), 'value'=>$this->getTranslations('location'), 'translate'=>true]));

        $this->form->addGroup($dateGroup);

        $descriptionGroup = new FormGroup('descriptionGroup', '__Description');

        $descriptionGroup->add(FormElement::textarea(['name'=>'summary', 'label'=>__('settings.common.summary'), 'value'=>$this->getTranslations('summary'), 'translate'=>true]));
        $descriptionGroup->add(FormElement::textarea(['name'=>'text', 'label'=>__('events.form.description'), 'value'=>$this->getTranslations('text'), 'translate'=>true])
            ->obligatory()
        );

        $this->form->addGroup($descriptionGroup);

        $this->includeAddons();

        $this->form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $this->form;
    }

    public function publicForm(): Form {
        $form = new Form($this->registerUrl);

        $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block_id]));
        $form->add(FormElement::hidden(['name'=>'event_id', 'value'=>$this->id]));
        $form->add(FormElement::text(['name'=>'name', 'label'=>__('settings.common.name'), 'value'=>old('name')]));
        $form->add(FormElement::email(['name'=>'email', 'label'=>__('events.registrationForm.email'), 'value'=>old('email')]));
        $form->add(FormElement::textarea(['name'=>'comment', 'label'=>__('events.registrationForm.comment')]));
        $form->add(FormElement::html(['value'=>'<div class="g-recaptcha" data-sitekey="'. env('RE_CAP_SITE') .'"></div>', 'name'=>'recaptcha']));

        $form->add(FormElement::submit(['value'=>__('events.registrationForm.register')]));

        $form->setErrorBag('errorsFrom'.ucfirst($this->block->type . $this->block_id));

        return $form;
    }

    public function registerForm() {
        return $this->publicForm()->render();
    }

    /**
     * @param Form $form
     * @return Form
     * @throws Exception
     */
    public function addCustomizationFormElements(Form &$form): Form {
        $this->addCropSetupGroup($form);

        if(\Auth::user()->role == "master"){
            $this->addResizePhotoSetupGroup($form);
        }

        $this->addItemRouteGroup($form);

        $registrationGroup = new FormGroup('registrationGroup', __('events.preferences.registration'), ['class'=>'col-md-6']);
        $registrationGroup->add(FormElement::text(['name'=>'successText', 'label'=>__('events.preferences.successfulMessage'), 'value'=>@$this->parameter('successText')]));
        $registrationGroup->add(FormElement::checkbox(['name'=>'notify', 'label'=>__('events.preferences.registrationNotification'), 'value'=>1, 'check'=>(@$this->parameter('notify')==1)]));
        $form->addGroup($registrationGroup);

        return $form;
    }

    public function handleItemForm(ValidateRequest $request) {
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775, true);
        }

        if(!is_null($request->file('image'))){
            $image = Image::make($request->file('image'));
        }

        if(is_null($request->id)){
            $event = new Events;
        }
        else{
            $event = Events::findOrNew($request->id);
        }
        $event->setBlock($request->block_id);
        if(!is_null($request->file('image'))){
            if(!empty($event->image)){
                $this->deleteImage($event->image);
            }

            $event->image = uniqid();
        }
        $event->start_date = $request->start_date . " " . $request->start_time;
        $event->end_date = !is_null($request->end_date) ? $request->end_date . " " . $request->end_time : null;
        $event->location = $request->location;
        $event->subject = $request->subject;
        $event->slug = $this->createSlug($request->subject['en']);
        $event->summary = $request->summary;
        $event->text = $request->text;
        $event->save();

        $this->handleAddons($request, $event);

        if(!is_null($request->file('image'))){
            $image->encode('png')->save(public_path('storage/'.$this->table.'/'.$event->image.".png"));

            if($this->parameter('cropPhoto')) {
                $event->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($event->image);
            }
        }

        return $event;
    }

    public function handlePublicForm(Request $request) {
        $event = Events::find($request->get('event_id'));
        $registration = new EventRegistration();

        $registration->event_id = $request->get('event_id');
        $registration->name = $request->get('name');
        $registration->email = $request->get('email');
        $registration->comment = $request->get('comment');

        $registration->save();

        if($this->parameter('notify') !== null){
            $admins = User::where('role', 'admin')->get();
            $page = $this->block->page();

            foreach ($admins as $admin){
                $admin->sendRegistrationNotifiaction($request->get('name') . ' (' . $request->get('email') . ')', $event, $request->get('comment'), $page->route);
            }
        }

        $message = $this->parameter('successText') ?? '';

        return $message;
    }

    public function registrations(){
        return $this->hasMany(EventRegistration::class,  'event_id');
    }

    public function itemCaption(): string {
        return $this->subject;
    }

    public function itemImage():string {
        return $this->imageSource(3);
    }
}
