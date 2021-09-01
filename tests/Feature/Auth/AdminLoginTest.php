<?php

namespace Just\Tests\Feature\Auth;

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
}
