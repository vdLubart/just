<?php

namespace Just\Controllers\Auth;

use Just\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Just\Models\Theme;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Show the application's login form.
     *
     * @override
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view(Theme::active()->name.'.system.auth.login');
    }
}
