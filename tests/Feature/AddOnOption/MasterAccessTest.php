<?php

namespace Just\Tests\Feature\AddOnOption;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test */
    function master_can_access_create_addon_option_form() {
        $this->access_create_addon_option_form(true);
    }

    /** @test */
    function master_can_access_actions_settings_page(){
        $this->access_actions_settings_page(true);
    }

    /** @test */
    function master_can_access_actions_settings_page_for_category(){
        $this->access_actions_settings_page_for_category(true);
    }

    /** @test */
    function master_can_access_actions_settings_page_for_tag(){
        $this->access_actions_settings_page_for_tag(true);
    }

    /** @test */
    function master_can_access_addon_option_list(){
        $this->access_addon_option_list(true);
    }

    /** @test */
    function master_can_activate_addon_option(){
        $this->activate_addon_option(true);
    }

    /** @test */
    function master_can_deactivate_addon_option(){
        $this->deactivate_addon_option(true);
    }

    /** @test */
    function master_can_move_addon_option_up(){
        $this->move_addon_option_up(true);
    }

    /** @test */
    function master_can_move_addon_option_down(){
        $this->move_addon_option_down(true);
    }

    /** @test */
    function master_can_delete_addon_option(){
        $this->delete_addon_option(true);
    }

    /** @test */
    function master_can_delete_addon_option_when_it_used_in_block(){
        $this->delete_addon_option_when_it_used_in_block(true);
    }
}
