<?php

namespace Tests\Browser\Auth;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\User;

class LoginTest extends DuskTestCase
{
    private $masterUser;
    
    private $adminUser;
    
    /** @test */
    public function master_can_login_to_admin_panel()
    {
        $this->masterUser = factory(User::class)->create(['role'=>'master']);
        
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin')
                    ->assertSee('Just!')
                    ->type('email', $this->masterUser->email)
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertSee($this->masterUser->name);
        });
        
        $this->masterUser->delete();
    }
    
    /** @test */
    public function admin_can_login_to_admin_panel()
    {
        $this->adminUser = factory(User::class)->create(['role'=>'admin']);
        
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin')
                    ->assertSee('Just!')
                    ->type('email', $this->adminUser->email)
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertSee($this->adminUser->name);
        });
        $this->closeAll();
        
        $this->adminUser->delete();
    }
}
