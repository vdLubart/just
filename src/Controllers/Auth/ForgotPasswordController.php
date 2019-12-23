<?php

namespace Just\Controllers\Auth;

use Just\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Just\Models\Theme;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    /**
     * Display the form to request a password reset link.
     *
     * @override
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view(Theme::active()->name.'.system.auth.passwords.email');
    }
}
