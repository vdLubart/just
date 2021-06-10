<?php

namespace Just\Tests\Feature\Users;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_see_user_list(){
        $this->see_user_list(false);
    }

    /** @test */
    function guest_cannot_create_new_admin(){
        $this->create_new_admin(false);
    }

    /** @test */
    function guest_cannot_create_new_master(){
        $this->create_new_admin(false);
    }

    /** @test */
    function guest_cannot_edit_user_email(){
        $this->edit_user_email(false);
    }

    /** @test */
    function guest_cannot_change_own_password(){
        $this->change_own_password(false);
    }

    /** @test */
    function guest_cannot_delete_user(){
        $this->delete_user(false);
    }
}
