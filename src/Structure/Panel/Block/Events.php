<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Just\Models\EventRegistration;
use Lubart\Just\Models\User;
use Lubart\Just\Requests\RegisterInEventRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Lubart\Just\Requests\ChangeEventRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Just\Structure\Page;
use Lubart\Just\Tools\Slug;

class Events extends AbstractBlock
{

    use Slug;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'summary', 'text', 'image', 'date_start', 'date_end', 'location'
    ];
    
    protected $table = 'events';
    
    /**
     * Title for model
     * 
     * @var string $settingsTitle
     */
    protected $settingsTitle = 'Event';

    protected $registerUrl = 'register-event';

    protected $neededParameters = [ 'itemRouteBase' ];

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
                'title' => 'Event',
                'description' => '',
                'route' => $this->block->parameter('itemRouteBase') . '/{id}',
                'layout_id' => $this->block->page->layout_id
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
    
    public function form() {
        if(is_null($this->form)){
            return;
        }

        $topGroup = new FormGroup('topGroup');

        $topGroup->add(FormElement::file(['name'=>'image', 'label'=>'Upload Image']));
        if(!is_null($this->id) and !empty($this->image)){
            if(file_exists(public_path('storage/'.$this->table.'/'.$this->image.'_3.png'))){
                $topGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'_3.png" />']));
            }
            else{
                $topGroup->add(FormElement::html(['name'=>'imagePreview'.'_'.$this->id, 'value'=>'<img src="/storage/'.$this->table.'/'.$this->image.'.png" width="300" />']));
            }

            if(!empty($this->parameter('cropPhoto'))) {
                $topGroup->add(FormElement::button(['name' => 'recrop', 'value' => 'Recrop Image']));
                $topGroup->getElement("recrop")->setParameters('javasript:openCropping(' . $this->block_id . ', ' . $this->id . ')', 'onclick');
            }
        }
        $topGroup->add(FormElement::text(['name'=>'subject', 'label'=>'Subject', 'value'=>$this->subject]));

        $this->form->addGroup($topGroup);

        $startDateGroup = new FormGroup('startDate', '', ['class'=>'col-md-6']);
        if(!empty($this->start_date)) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date);
            $startDateGroup->add(FormElement::date(['name' => 'start_date', 'label' => 'Start Date', 'value' => $startDate->format('Y-m-d')]));
            $startDateGroup->add(FormElement::time(['name' => 'start_time', 'label' => 'Start Time', 'value' => $startDate->format('H:i')]));
        }
        else{
            $startDateGroup->add(FormElement::date(['name' => 'start_date', 'label' => 'Start Date']));
            $startDateGroup->add(FormElement::time(['name' => 'start_time', 'label' => 'Start Time']));
        }

        $endDateGroup = new FormGroup('endDate', '', ['class'=>'col-md-6']);
        if(!empty($this->end_date)) {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->end_date);
            $endDateGroup->add(FormElement::date(['name' => 'end_date', 'label' => 'End Date', 'value' => $endDate->format('Y-m-d')]));
            $endDateGroup->add(FormElement::time(['name' => 'end_time', 'label' => 'End Time', 'value' => $endDate->format('H:i')]));
        }
        else{
            $endDateGroup->add(FormElement::date(['name' => 'end_date', 'label' => 'End Date']));
            $endDateGroup->add(FormElement::time(['name' => 'end_time', 'label' => 'End Time']));
        }

        $this->form->addGroup($startDateGroup);
        $this->form->addGroup($endDateGroup);

        $this->form->add(FormElement::text(['name'=>'location', 'label'=>'Location', 'value'=>$this->location]));

        $this->form->add(FormElement::textarea(['name'=>'summary', 'label'=>'Summary', 'value'=>$this->summary]));
        $this->form->add(FormElement::textarea(['name'=>'text', 'label'=>'Event Description', 'value'=>$this->text]));

        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Save']));
        
        $this->form->applyJS("
$(document).ready(function(){
    CKEDITOR.replace('summary');
    CKEDITOR.replace('text');
});");
        
        return $this->form;
    }

    public function registerForm() {
        $form = new Form($this->registerUrl);

        $form->add(FormElement::hidden(['name'=>'block_id', 'value'=>$this->block_id]));
        $form->add(FormElement::hidden(['name'=>'event_id', 'value'=>$this->id]));
        $form->add(FormElement::text(['name'=>'name', 'label'=>'Name', 'value'=>old('name')]));
        $form->add(FormElement::email(['name'=>'email', 'label'=>'Email', 'value'=>old('email')]));
        $form->add(FormElement::textarea(['name'=>'comment', 'label'=>'Leave a comment']));

        $form->add(FormElement::submit(['value'=>'Register']));

        return $form->render();
    }

    public function addSetupFormElements(Form &$form) {
        $this->addCropSetupGroup($form);

        if(\Auth::user()->role == "master"){
            $this->addResizePhotoSetupGroup($form);
        }

        $this->addItemRouteGroup($form);

        $registrationGroup = new FormGroup('registrationGroup', 'Registration Settings', ['class'=>'col-md-6']);
        $registrationGroup->add(FormElement::text(['name'=>'successText', 'label'=>'Message after successful registration', 'value'=>@$this->parameter('successText')]));
        $registrationGroup->add(FormElement::checkbox(['name'=>'notify', 'label'=>'Notify me by email about new registration', 'value'=>1, 'check'=>(@$this->parameter('notify')==1)]));
        $form->addGroup($registrationGroup);

        $form->useJSFile('/js/blocks/setupForm.js');

        return $form;
    }
    
    public function handleForm(ChangeEventRequest $request) {
        if(!file_exists(public_path('storage/'.$this->table))){
            mkdir(public_path('storage/'.$this->table), 0775);
        }
        
        if(!is_null($request->file('image'))){
            $image = Image::make($request->file('image'));
        }
        
        if(is_null($request->get('id'))){
            $event = new Events;
        }
        else{
            $event = Events::findOrNew($request->get('id'));
        }
        $event->setBlock($request->get('block_id'));
        if(!is_null($request->file('image'))){
            $event->image = uniqid();
        }
        $event->start_date = $request->get('start_date') . ":" . $request->get('start_time');
        $event->end_date = $request->get('end_date') . ":" . $request->get('end_time');
        $event->location = $request->get('location');
        $event->subject = $request->get('subject');
        $event->slug = $this->createSlug($request->get('subject'));
        $event->summary = $request->get('summary');
        $event->text = $request->get('text');
        $event->save();
        
        $this->handleAddons($request, $event);
        
        if(!is_null($request->file('image'))){
            $image->encode('png')->save(public_path('storage/'.$this->table.'/'.$event->image.".png"));

            if($this->parameter('shouldBeCropped')) {
                $event->shouldBeCropped = true;
            }
            else{
                $this->multiplicateImage($event->image);
            }
        }
        
        return $event;
    }

    public function handlePublicForm(RegisterInEventRequest $request) {
        $event = Events::find($request->get('event_id'));
        $registration = new EventRegistration();

        $registration->event_id = $request->get('event_id');
        $registration->name = $request->get('name');
        $registration->email = $request->get('email');
        $registration->comment = $request->get('comment');

        $registration->save();

        if($this->parameter('notify') !== null){
            $admins = User::where('role', 'admin')->get();
            $page = $this->block->page;
            if(is_null($page) and is_null($this->block->location)){
                $page = $this->block->page;
            }
            elseif(!is_null($this->block->location)){
                $page = Page::first();
            }
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
}
