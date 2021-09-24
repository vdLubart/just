<?php

namespace Just\Tests\Feature\Pages;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_access_actions_page(){
        $this->access_actions_page(false);
    }

    /** @test */
    function guest_cannot_create_new_page(){
        $this->create_new_page(false);
    }

    /** @test */
    function guest_cannot_setup_current_page(){
        $this->setup_current_page(false);
    }

    /** @test */
    function guest_cannot_apply_meta_to_all_pages(){
        $this->apply_meta_to_all_pages(false);
    }

    /** @test */
    function guest_cannot_access_page_list(){
        $this->access_page_list(false);
    }

    /** @test */
    function guest_cannot_edit_specific_page(){
        $this->edit_specific_page(false);
    }

    /** @test */
    function guest_cannot_delete_specific_page(){
        $this->delete_specific_page(false);
    }

    /** @test */
    function guest_cannot_access_page_panel_list(){
        $this->access_page_panel_list(false);
    }

    /** @test */
    function guest_cannot_activate_page(){
        $this->activate_page(false);
    }

    /** @test */
    function guest_cannot_deactivate_page(){
        $this->deactivate_page(false);
    }
}
