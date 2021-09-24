<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Tests\Feature\Auth;

use Tests\TestCase;

class ForgotPasswordTest extends TestCase {

    /** @test */
    function guest_can_access_forgot_password_page(){
        $this->get("password/reset")
            ->assertSuccessful()
            ->assertViewIs('Just.system.auth.passwords.email');
    }

    /** @test */
    function guest_can_access_reset_password_page(){
        $this->get("password/reset/somecode")
            ->assertSuccessful()
            ->assertViewIs('Just.system.auth.passwords.reset');
    }

}
