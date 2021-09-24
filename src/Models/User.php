<?php

namespace Just\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Just\Database\Factories\UserFactory;
use Just\Notifications\NewRegistration;
use Just\Notifications\PasswordReset;
use Just\Notifications\NewFeedback;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Just\Requests\SaveUserRequest;
use App\User as AppUser;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperUser
 */
class User extends AppUser
{
    use Notifiable;
    use HasFactory;

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
        $form = new Form('/settings/user/password/update');

        $form->add(FormElement::password(['name'=>'current_password', 'label'=>__('user.changePasswordForm.currentPassword')])
            ->obligatory()
        );
        $form->add(FormElement::password(['name'=>'new_password', 'label'=>__('user.changePasswordForm.newPassword')])
            ->obligatory()
        );
        $form->add(FormElement::password(['name'=>'new_password_confirmation', 'label'=>__('user.changePasswordForm.confirmNewPassword')])
            ->obligatory()
        );
        $form->add(FormElement::submit(['value'=>__('user.changePasswordForm.action')]));

        return $form;
    }

    /**
     * Return form to create a new page
     *
     * @return Form
     * @throws Exception
     */
    public function itemForm(): Form {
        return $this->settingsForm();
    }

    /**
     * Get page settings form
     *
     * @return Form
     * @throws Exception
     */
    public function settingsForm(): Form {
        $form = new Form('settings/user/setup');

        $form->add(FormElement::hidden(['name'=>'user_id', 'value'=>@$this->id])
            ->obligatory()
        );
        $form->add(FormElement::email(['name'=>'email', 'label'=>__('user.createForm.login'), 'value'=>@$this->email])
            ->obligatory()
        );
        $form->add(FormElement::text(['name'=>'name', 'label'=>__('user.createForm.name'), 'value'=>@$this->name])
            ->obligatory()
        );
        if(!$this->id){
            $form->add(FormElement::password(['name'=>'password', 'label'=>__('user.createForm.password')])
                ->obligatory()
            );
            $form->add(FormElement::password(['name'=>'password_confirmation', 'label'=>__('user.createForm.confirmPassword')])
                ->obligatory()
            );
        }
        $form->add(FormElement::select(['name'=>'role', 'label'=>__('user.createForm.role'), 'options'=>['master'=>'master', 'admin'=>'admin'], 'value'=>@$this->role])
            ->obligatory()
        );
        $form->add(FormElement::submit(['value'=>__('settings.actions.save')]));

        return $form;
    }

    public function handleSettingsForm(SaveUserRequest $request) {
        $this->name = $request->name;
        $this->email = $request->email;
        $this->role = $request->role;
        if(isset($request->password)){
            $this->password = bcrypt($request->password);
        }

        $this->save();
    }

    /**
     * Check if user has admin rights. User should has admin or master role
     *
     * @return bool
     */
    public function isAdmin(): bool {
        return in_array($this->role, ['admin', 'master']);
    }

    /**
     * Check if user has master access rights
     *
     * @return bool
     */
    public function isMaster(): bool {
        return $this->role === 'master';
    }

    public static function authAsAdmin(): bool {
        return Auth::check() and User::find(Auth::id())->isAdmin();
    }

    public static function authAsMaster(): bool {
        return Auth::check() and User::find(Auth::id())->isMaster();
    }

    /**
     * Return caption for page item in the page list
     *
     * @return string
     */
    public function itemCaption(): string {
        return ($this->name === '' ? __('block.untitled') : $this->name);
    }

    protected static function newFactory(): Factory {
        return UserFactory::new();
    }
}
