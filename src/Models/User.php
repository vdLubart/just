<?php

namespace Lubart\Just\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lubart\Just\Notifications\PasswordReset;
use Lubart\Just\Notifications\NewFeedback;
use Lubart\Form\Form;
use Lubart\Form\FormElement;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
    
    public static function changePasswordForm() {
        $form = new Form('/admin/settings/password/update');
        
        $form->add(FormElement::password(['name'=>'current_password', 'label'=>'Enter current password']));
        $form->add(FormElement::password(['name'=>'new_password', 'label'=>'Enter new password']));
        $form->add(FormElement::password(['name'=>'new_password_confirmation', 'label'=>'Confirm new password']));
        $form->add(FormElement::submit(['value'=>'Change Password']));
        
        return $form;
    }
}
