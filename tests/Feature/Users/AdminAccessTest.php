<?php

namespace Just\Tests\Feature\Users;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test */
    function admin_cannot_access_actions_page(){
        $this->access_actions_page(false);
    }

    /** @test */
    function admin_cannot_see_user_list(){
        $this->see_user_list(false);
    }

    /** @test */
    function admin_cannot_create_new_admin(){
        $this->create_new_admin(false);
    }

    /** @test */
    function admin_cannot_create_new_master(){
        $this->create_new_master(false);
    }

    /** @test */
    function admin_cannot_edit_user_email(){
        $this->edit_user_email(false);
    }

    /** @test */
    function admin_can_change_own_password(){
        $this->change_own_password(true);
    }

    /** @test */
    function admin_cannot_change_own_password_without_current_password(){
        $this->cannot_change_own_password_without_current_one();
    }

    /** @test */
    function admin_cannot_delete_user(){
        $this->delete_user(false);
    }

    /** @test */
    function admin_cannot_delete_yourself(){
        $this->delete_yourself(false);
    }

    /** @test */
    function admin_cannot_activate_user(){
        $this->activate_user(false);
    }

    /** @test */
    function admin_cannot_deactivate_user(){
        $this->deactivate_user(false);
    }
}
