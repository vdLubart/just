<?php

namespace Just\Structure\Panel\Block;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Just\Structure\Panel\Block\Contracts\ContainsPublicForm;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;
use Just\Tools\Useful;
use Just\Models\Route as JustRoute;
use Just\Models\User;

class Feedback extends AbstractBlock implements ContainsPublicForm
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'message', 'isActive'
    ];
    
    protected $table = 'feedbacks';
    
    protected $neededParameters = [];
    
    public function settingsTitle() {
        return __('feedback.title');
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        $this->form->add(FormElement::text(['name'=>'username', 'label'=>__('settings.common.name'), 'value'=>$this->username]));
        $this->form->add(FormElement::email(['name'=>'email', 'label'=>__('feedback.form.email'), 'value'=>$this->email]));
        if($this->id){
           $this->form->add(FormElement::date(['name'=>'created', 'label'=>__('feedback.form.date'), 'value'=>$this->created_at]));
        }
        $this->form->add(FormElement::textarea(['name'=>'message', 'label'=>__('feedback.form.message'), 'value'=>$this->message]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>__('settings.actions.submit')]));
        
        return $this->form;
    }
    
    public function setup() {
        if(!Useful::isRouteExists("feedback/add")){
            JustRoute::create([
                'route' => "feedback/add",
                'type' => 'post',
                'block_id' => $this->block_id
            ]);
        }
    }
    
    public function addSetupFormElements(Form &$form) {
        $feedbackGroup = new FormGroup('feedbackGroup', __('feedback.preferences.title'), ['class'=>'col-nd-6']);
        $feedbackGroup->add(FormElement::radio(['name'=>'defaultActivation', 'label'=>__('feedback.preferences.noModeration'), 'value'=>1, 'check'=>(@$this->parameter('defaultActivation')==1)]));
        $feedbackGroup->add(FormElement::radio(['name'=>'defaultActivation', 'label'=>__('feedback.preferences.moderation'), 'value'=>0, 'check'=>(@$this->parameter('defaultActivation')==0)]));
        $feedbackGroup->add(FormElement::text(['name'=>'successText', 'label'=>__('feedback.preferences.successMessage'), 'value'=>@$this->parameter('successText')]));
        $feedbackGroup->add(FormElement::checkbox(['name'=>'notify', 'label'=>__('feedback.preferences.notifyMe'), 'value'=>1, 'check'=>(@$this->parameter('notify')==1)]));
        $form->addGroup($feedbackGroup);
        
        return $form;
    }
    
    /**
     * Handle request from the settings form
     * 
     * @param ChangeFeedbackRequest $request
     * @return Feedback
     */
    public function handleForm(ValidateRequest $request) {
        if(is_null($request->get('id'))){
            $feedback = new Feedback;
            $feedback->orderNo = Useful::getMaxNo($this->table, ['block_id'=>$request->get('block_id')]);
        }
        else{
            $feedback = Feedback::findOrNew($request->get('id'));
            $time = \Carbon\Carbon::parse($feedback->created_at)->format("H:i:s");
            $feedback->created_at = $request->get('created')." ".$time;
        }
        
        $feedback->setBlock($request->get('block_id'));
        $feedback->username = $request->get('username');
        $feedback->email = $request->get('email');
        $feedback->message = $request->get('message');
        
        $feedback->save();
        
        $this->handleAddons($request, $feedback);
        
        return $feedback;
    }

    public function publicForm() {
        $form = new Form("/feedback/add");

        $form->add(FormElement::hidden(['name'=>"block_id", "value"=>$this->block_id]));
        $form->add(FormElement::text(['name'=>'username', 'label'=>__('settings.common.name'), 'value'=>$this->username]));
        $form->add(FormElement::email(['name'=>'email', 'label'=>__('feedback.form.email'), 'value'=>$this->email]));
        $form->add(FormElement::textarea(['name'=>'message', 'label'=>__('feedback.form.message'), 'value'=>$this->message]));
        $form->add(FormElement::html(['value'=>'<div class="g-recaptcha" data-sitekey="'. env('RE_CAP_SITE') .'"></div>', 'name'=>'recaptcha']));
        $form->add(FormElement::submit(['value'=>__('settings.actions.submit')]));

        $form->setErrorBag('errorsFrom'.ucfirst($this->block->type . $this->block_id));

        return $form;
    }
    
    public function feedbackForm() {
        return $this->publicForm()->render();
    }

    public function handlePublicForm(Request $request) {
        $parameters = $this->block->parameters;
        
        $this->block_id = $this->block_id;
        $this->username = $request->get('username');
        $this->email = $request->get('email');
        $this->message = $request->get('message');
        $this->orderNo = Useful::getMaxNo($this->table, ['block_id' => $request->get('block_id')]);
        $this->isActive = $parameters->defaultActivation ?? 0;
        
        $this->save();
        
        Useful::normalizeOrder($this->table);
        
        if(isset($parameters->notify)){
            $admins = User::where('role', 'admin')->get();
            $block = $this->block;
            $page = $this->block->page();

            foreach ($admins as $admin){
                $admin->sendFeedbackNotifiaction($request->get('username'), $request->get('message'), $block->title, $page->route);
            }
        }
        
        $message = $parameters->successText ?? '';
        
        return $message;
    }
}
