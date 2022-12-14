<?php

namespace Just\Tests\Feature\Layouts;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_access_actions_page(){
        $this->access_actions_page(false);
    }

    /** @test */
    function guest_cannot_change_default_layout(){
        $this->cannot_change_default_layout();
    }

    /** @test */
    function guest_cannot_create_new_layout(){
        $this->create_new_layout(false);
    }

    /** @test */
    function admin_cannot_create_layout_with_existing_class(){
        $this->cannot_create_layout_with_existing_class();
    }

    /** @test */
    function guest_cannot_choose_default_layout(){
        $this->choose_default_layout(false);
    }

    /** @test */
    function guest_cannot_access_layout_list(){
        $this->access_layout_list(false);
    }

    /** @test */
    function guest_cannot_delete_layout(){
        $this->delete_layout(false);
    }

    /** @test */
    function guest_can_get_layout_from_the_panel(){
        $this->get_layout_from_the_panel();
    }
}
