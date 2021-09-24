<?php

namespace Just\Tests\Feature\Auth;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Just\Models\User;

class AdminLoginTest extends TestCase
{
    /** @test */
    function admin_user_can_access_login_page(){
        $this->get('admin')
                ->assertRedirect('login');

        $this->get('login')
                ->assertSuccessful();
    }

    /** @test */
    function admin_user_can_login_with_initial_credentials(){
        $this->post('login', [
            'email' => 'admin@just-use.it',
            'password' => 'admin'
        ])
                ->assertRedirect('admin');

        $adminUser = User::where('email', 'admin@just-use.it')->first();

        $this->assertAuthenticatedAs($adminUser);
    }

    /** @test */
    function inactive_admin_cannot_login(){
        $user = User::find(1);
        $user->isActive = 0;
        $user->save();

        $this->post('login', [
            'email' => 'admin@just-use.it',
            'password' => 'admin'
        ])
            ->assertRedirect('/');

        $this->assertFalse(Auth::check());

        $user->isActive = 1;
        $user->save();
    }
}
