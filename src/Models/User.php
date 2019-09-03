<?php

namespace Lubart\Just\Models;

use Illuminate\Notifications\Notifiable;
use Lubart\Just\Notifications\NewRegistration;
use Lubart\Just\Notifications\PasswordReset;
use Lubart\Just\Notifications\NewFeedback;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Just\Requests\UserChangeRequest;
use App\User as AppUser;

class User extends AppUser
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'role', 'name', 'email', 'password',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new PasswordReset($token));
    }
    
    public function sendFeedbackNotifiaction($username, $message, $blockTitle, $route) {
        $this->notify(new NewFeedback($username, $message, $blockTitle, $route));
    }

    public function sendRegistrationNotifiaction($username, $event, $comment, $route) {
        $this->notify(new NewRegistration($username, $event, $comment, $route));
    }
    
    public static function changePasswordForm() {
        $form = new Form('/admin/settings/password/update');
        
        $form->add(FormElement::password(['name'=>'current_password', 'label'=>__('user.changePasswordForm.currentPassword')]));
        $form->add(FormElement::password(['name'=>'new_password', 'label'=>__('user.changePasswordForm.newPassword')]));
        $form->add(FormElement::password(['name'=>'new_password_confirmation', 'label'=>__('user.changePasswordForm.confirmNewPassword')]));
        $form->add(FormElement::submit(['value'=>__('user.changePasswordForm.action')]));
        
        return $form;
    }
    
    /**
     * Get page settings form
     * 
     * @return Form
     */
    public function settingsForm() {
        $form = new Form('admin/settings/user/setup');
        
        $form->add(FormElement::hidden(['name'=>'user_id', 'value'=>@$this->id]));
        $form->add(FormElement::email(['name'=>'email', 'label'=>__('user.createForm.login'), 'value'=>@$this->email]));
        $form->add(FormElement::text(['name'=>'name', 'label'=>__('user.createForm.name'), 'value'=>@$this->name]));
        $form->add(FormElement::select(['name'=>'role', 'label'=>__('user.createForm.role'), 'options'=>['master'=>'master', 'admin'=>'admin'], 'value'=>@$this->role]));
        if(!$this->id){
            $form->add(FormElement::password(['name'=>'password', 'label'=>__('user.createForm.password')]));
            $form->add(FormElement::password(['name'=>'password_confirmation', 'label'=>__('user.createForm.confirmPassword')]));
        }
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));
        
        return $form;
    }
    
    public function handleSettingsForm(UserChangeRequest $request) {
        $this->name = $request->name;
        $this->email = $request->email;
        $this->role = $request->role;
        if(isset($request->password)){
            $this->password = bcrypt($request->password);
        }
        
        $this->save();
    }
}
