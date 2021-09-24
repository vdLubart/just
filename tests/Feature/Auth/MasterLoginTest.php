<?php

namespace Just\Tests\Feature\Auth;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Just\Models\User;

class MasterLoginTest extends TestCase
{
    /** @test */
    function master_user_can_access_login_page(){
        $this->get('admin')
                ->assertRedirect('login');

        $this->get('login')
                ->assertSuccessful();
    }

    /** @test */
    function master_user_can_login_with_initial_credentials(){
        $this->post('login', [
            'email' => 'master@just-use.it',
            'password' => 'master'
        ])
                ->assertRedirect('admin');

        $masterUser = User::where('email', 'master@just-use.it')->first();

        $this->assertAuthenticatedAs($masterUser);
    }

    /** @test */
    function inactive_master_cannot_login(){
        $user = User::find(2);
        $user->isActive = 0;
        $user->save();

        $this->post('login', [
            'email' => 'master@just-use.it',
            'password' => 'master'
        ])
            ->assertRedirect('/');

        $this->assertFalse(Auth::check());

        $user->isActive = 1;
        $user->save();
    }
}
