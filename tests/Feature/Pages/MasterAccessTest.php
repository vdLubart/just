<?php

namespace Just\Tests\Feature\Pages;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test */
    function master_can_access_actions_page(){
        $this->access_actions_page(true);
    }

    /** @test */
    function master_can_create_new_page(){
        $this->create_new_page(true);
    }

    /** @test */
    function master_can_setup_current_page(){
        $this->setup_current_page(true);
    }

    /** @test */
    function master_can_apply_meta_to_all_pages(){
        $this->apply_meta_to_all_pages(true);
    }

    /** @test */
    function master_can_access_page_list(){
        $this->access_page_list(true);
    }

    /** @test */
    function master_can_edit_specific_page(){
        $this->edit_specific_page(true);
    }

    /** @test */
    function master_can_delete_specific_page(){
        $this->delete_specific_page(true);
    }

    /** @test */
    function master_can_access_page_panel_list(){
        $this->access_page_panel_list(true);
    }

    /** @test */
    function master_can_activate_page(){
        $this->activate_page(true);
    }

    /** @test */
    function master_can_deactivate_page(){
        $this->deactivate_page(true);
    }
}
