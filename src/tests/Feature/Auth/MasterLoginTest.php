<?php

namespace Lubart\Just\Tests\Feature\Auth;

use Tests\TestCase;
use Lubart\Just\Models\User;

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
}
