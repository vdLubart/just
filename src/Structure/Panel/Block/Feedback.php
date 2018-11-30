<?php

namespace Lubart\Just\Structure\Panel\Block;

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Models\Route as JustRoute;
use Lubart\Just\Requests\AddFeedbackRequest;
use Lubart\Just\Requests\FeedbackChangeRequest;
use Lubart\Just\Models\User;
use Lubart\Just\Structure\Page;

class Feedback extends AbstractBlock
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
    
    protected $settingsTitle = 'Comment';
    
    protected $neededParameters = [];
    
    private $content;
    
    public function content($id = null) {
        if(!empty($this->content)){
            return $this->content;
        }
        
        $this->content = new \stdClass();
        
        if(is_null($id)){
            $messages = $this->orderBy('orderNo', 'desc')
                    ->where('block_id', $this->block_id);
            if(!\Config::get('isAdmin')){
                $messages = $messages->where('isActive', 1);
            }
            
            $this->content->messages = $messages->get();
        }
        else{
            $this->content->messages = $messages->find($id);
        }
        $this->content->form = $this->feedbackForm();
        
        return $this->content;
    }
    
    public function form() {
        if(!is_null($this->id)){
            $this->form->open();
        }
        
        $this->form->add(FormElement::text(['name'=>'username', 'label'=>'Name', 'value'=>$this->username]));
        $this->form->add(FormElement::email(['name'=>'email', 'label'=>'Email', 'value'=>$this->email]));
        if($this->id){
           $this->form->add(FormElement::date(['name'=>'created', 'label'=>'Date', 'value'=>$this->created_at])); 
        }
        $this->form->add(FormElement::textarea(['name'=>'message', 'label'=>'Message', 'value'=>$this->message]));
        
        $this->includeAddons();
        
        $this->form->add(FormElement::submit(['value'=>'Submit']));
        
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
        $feedbackGroup = new FormGroup('feedbackGroup', 'Feedback Settings', ['class'=>'col-nd-6']);
        $feedbackGroup->add(FormElement::radio(['name'=>'defaultActivation', 'label'=>'Feedback is visible just after publishing', 'value'=>1, 'check'=>(@$this->parameter('defaultActivation')==1)]));
        $feedbackGroup->add(FormElement::radio(['name'=>'defaultActivation', 'label'=>'Admin must confirm feedback publishing', 'value'=>0, 'check'=>(@$this->parameter('defaultActivation')==0)]));
        $feedbackGroup->add(FormElement::text(['name'=>'successText', 'label'=>'Message after successful publishing feedback', 'value'=>@$this->parameter('successText')]));
        $feedbackGroup->add(FormElement::checkbox(['name'=>'notify', 'label'=>'Notify me by email about a new feedback', 'value'=>1, 'check'=>(@$this->parameter('notify')==1)]));
        $form->addGroup($feedbackGroup);
        
        return $form;
    }
    
    /**
     * Handle request from the settings form
     * 
     * @param FeedbackChangeRequest $request
     * @return Feedback
     */
    public function handleForm(FeedbackChangeRequest $request) {
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
    
    private function feedbackForm() {
        $form = new \stdClass;
        $form = new Form("/feedback/add");
        
        $form->add(FormElement::hidden(['name'=>"block_id", "value"=>$this->block_id]));
        $form->add(FormElement::text(['name'=>'username', 'label'=>'Name', 'value'=>$this->username]));
        $form->add(FormElement::email(['name'=>'email', 'label'=>'Email', 'value'=>$this->email]));
        $form->add(FormElement::textarea(['name'=>'message', 'label'=>'Message', 'value'=>$this->message]));
        $form->add(FormElement::html(['value'=>'<div class="g-recaptcha" data-sitekey="'. env('RE_CAP_SITE') .'"></div>', 'name'=>'recaptcha']));
        $form->add(FormElement::submit(['value'=>'Submit']));
        
        $form->setErrorBag('errorsFrom'.ucfirst($this->block()->name . $this->block_id));
        
        return $form;
    }
    
    public function handlePublicForm(AddFeedbackRequest $request) {
        $parameters = json_decode($this->block()->parameters);
        
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
            $page = $this->block->page;
            if(is_null($page) and is_null($this->block->location)){
                $block = $this->block->parentBlock(true);
                $page = $block->page;
            }
            elseif(!is_null($this->block->location)){
                $page = Page::first();
            }
            foreach ($admins as $admin){
                $admin->sendFeedbackNotifiaction($request->get('username'), $request->get('message'), $block->title, $page->route);
            }
        }
        
        $message = $parameters->successText ?? '';
        
        return $message;
    }
}
