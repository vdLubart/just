<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Events;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test*/
    function master_cannot_access_block_setup_without_initial_setup(){
        $this->access_item_form_without_initial_data(false);
    }
    
    /** @test */
    function master_can_access_item_form_when_block_is_setted_up(){
        $this->access_item_form_when_block_is_setted_up(true);
    }
    
    /** @test */
    function master_can_access_item_edit_form(){
        $this->access_edit_item_form(true);
    }
    
    /** @test */
    function master_can_create_item_in_the_block(){
        $this->create_new_item_in_block(true);
    }

    /** @test */
    function master_can_create_item_in_the_block_with_addon(){
        $this->create_event_with_addon(true);
    }
    
    /** @test */
    function master_recieves_an_error_on_sending_incompleate_create_item_form(){
        $this->receive_an_error_on_sending_incompleate_create_item_form(true);
    }
    
    /** @test */
    function master_can_edit_item_in_the_block(){
        $this->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function master_can_access_created_item(){
        $this->access_created_item(true);
    }
    
    /** @test */
    function master_can_edit_block_settings(){
        $this->edit_block_settings(true);
    }

    /** @test */
    function master_can_fetch_events_for_current_category(){
        $this->get_events_from_the_current_category();
    }

    /** @test */
    function master_can_create_item_with_standard_image_sizes(){
        $this->create_item_with_standard_image_sizes();
    }

    /** @test */
    function master_can_create_item_with_custom_image_sizes(){
        $this->create_item_with_custom_image_sizes();
    }

    /** @test */
    function master_can_create_item_with_empty_custom_image_sizes(){
        $this->create_item_with_empty_custom_image_sizes();
    }

    /** @test */
    function master_can_register_on_event(){
        $this->register_on_event();
    }

    /** @test */
    function master_can_register_on_event_without_comment(){
        $this->register_on_event_without_comment();
    }

    /** @test */
    function master_cannot_register_on_event_without_name(){
        $this->cannot_register_on_event_without_name();
    }

    /** @test */
    function master_cannot_register_on_event_without_email(){
        $this->cannot_register_on_event_without_email();
    }

    /** @test */
    function master_cannot_register_on_event_twice(){
        $this->cannot_register_on_event_twice();
    }

    /** @test */
    function admin_is_notified_about_masters_registration_in_event(){
        $this->admin_is_notified_about_new_registration_on_event();
    }
}
