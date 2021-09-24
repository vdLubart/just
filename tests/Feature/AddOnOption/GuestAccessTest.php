<?php

namespace Just\Tests\Feature\AddOnOption;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_access_create_addon_option_form() {
        $this->access_create_addon_option_form(false);
    }

    /** @test */
    function guest_cannot_access_actions_settings_page(){
        $this->access_actions_settings_page(false);
    }

    /** @test */
    function guest_cannotnot_access_actions_settings_page_for_category(){
        $this->access_actions_settings_page_for_category(false);
    }

    /** @test */
    function guest_cannot_access_actions_settings_page_for_tag(){
        $this->access_actions_settings_page_for_tag(false);
    }

    /** @test */
    function guest_cannot_access_addon_option_list(){
        $this->access_addon_option_list(false);
    }

    /** @test */
    function guest_cannot_activate_addon_option(){
        $this->activate_addon_option(false);
    }

    /** @test */
    function guest_cannot_deactivate_addon_option(){
        $this->deactivate_addon_option(false);
    }

    /** @test */
    function guest_cannot_move_addon_option_up(){
        $this->move_addon_option_up(false);
    }

    /** @test */
    function guest_cannot_move_addon_option_down(){
        $this->move_addon_option_down(false);
    }

    /** @test */
    function guest_cannot_delete_addon_option(){
        $this->delete_addon_option(false);
    }

    /** @test */
    function guest_cannot_delete_addon_option_when_it_used_in_block(){
        $this->delete_addon_option_when_it_used_in_block(false);
    }
}
