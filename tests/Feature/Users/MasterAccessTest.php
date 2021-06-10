<?php

namespace Just\Tests\Feature\Users;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test */
    function master_can_see_user_list(){
        $this->see_user_list(true);
    }

    /** @test */
    function master_can_create_new_admin(){
        $this->create_new_admin(true);
    }

    /** @test */
    function master_can_create_new_master(){
        $this->create_new_admin(true);
    }

    /** @test */
    function master_can_edit_user_email(){
        $this->edit_user_email(true);
    }

    /** @test */
    function master_can_change_own_password(){
        $this->change_own_password(true);
    }

    /** @test */
    function master_cannot_change_own_password_without_current_password(){
        $this->cannot_change_own_password_without_current_one();
    }

    /** @test */
    function master_can_delete_user(){
        $this->delete_user(true);
    }

    /** @test */
    function master_cannot_delete_yourself(){
        $this->delete_yourself(false);
    }
}
