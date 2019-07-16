<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Events;

class GuestAccessTest extends Actions
{
    
    /** @test */
    function guest_cannot_access_item_create_form(){
        $this->access_item_form_when_block_is_setted_up(false);
    }
    
    /** @test */
    function guest_cannot_access_item_edit_form(){
        $this->access_edit_item_form(false);
    }
    
    /** @test */
    function guest_cannot_create_item_in_the_block(){
        $this->create_new_item_in_block(false);
    }

    /** @test */
    function guest_cannot_create_item_in_the_block_with_addon(){
        $this->create_event_with_addon(false);
    }
    
    /** @test */
    function guest_cannot_recieve_an_error_on_sending_incompleate_create_item_form(){
        $this->receive_an_error_on_sending_incompleate_create_item_form(false);
    }
    
    /** @test */
    function guest_cannot_edit_item_in_the_block(){
        $this->edit_existing_item_in_the_block(false);
    }
    
    /** @test */
    function guest_can_access_created_item(){
        $this->access_created_item(true);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->edit_block_settings(false);
    }

    /** @test */
    function guest_can_register_on_event(){
        $this->register_on_event();
    }

    /** @test */
    function guest_can_register_on_event_without_comment(){
        $this->register_on_event_without_comment();
    }

    /** @test */
    function guest_cannot_register_on_event_without_name(){
        $this->cannot_register_on_event_without_name();
    }

    /** @test */
    function guest_cannot_register_on_event_without_email(){
        $this->cannot_register_on_event_without_email();
    }

    /** @test */
    function guest_cannot_register_on_event_twice(){
        $this->cannot_register_on_event_twice();
    }

    /** @test */
    function admin_is_notified_about_guests_registration_in_event(){
        $this->admin_is_notified_about_new_registration_on_event();
    }
}
