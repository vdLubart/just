<?php

namespace Just\Tests\Feature\Blocks\Events;

class GuestAccessTest extends Actions
{

    /** @test */
    function guest_cannot_access_item_create_form(){
        $this->inContent()->access_item_form(false);
        $this->inHeader()->access_item_form(false);
        //$this->relatedBlock()->access_item_form(false);
    }

    /** @test */
    function guest_cannot_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(false);
        $this->inHeader()->access_edit_item_form(false);
        //$this->relatedBlock()->access_edit_item_form(false);
    }

    /** @test */
    function guest_cannot_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(false);
        $this->inHeader()->create_new_item_in_block(false);
        //$this->relatedBlock()->create_new_item_in_block(false);
    }

    /** @test */
    function guest_cannot_receive_an_error_on_sending_incomplete_create_item_form(){
        $this->inContent()->receive_an_error_on_sending_incomplete_create_item_form(false);
        $this->inHeader()->receive_an_error_on_sending_incomplete_create_item_form(false);
        //$this->relatedBlock()->receive_an_error_on_sending_incomplete_create_item_form(false);
    }

    /** @test */
    function guest_cannot_receive_an_error_on_wrong_date_and_time_format(){
        $this->inContent()->receive_an_error_on_wrong_date_and_time_format(false);
        $this->inHeader()->receive_an_error_on_wrong_date_and_time_format(false);
        //$this->relatedBlock()->receive_an_error_on_wrong_date_and_time_format(false);
    }

    /** @test */
    function guest_cannot_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(false);
        $this->inHeader()->edit_existing_item_in_the_block(false);
        //$this->relatedBlock()->edit_existing_item_in_the_block(false);
    }

    /** @test */
    function guest_can_access_created_item(){
        $this->inContent()->access_created_item(true);
        $this->inHeader()->access_created_item(true);
        //$this->relatedBlock()->access_created_item(true);
    }

    /** @test */
    function guest_cannot_customize_block(){
        $this->inContent()->customize_block(false);
        $this->inHeader()->customize_block(false);
        //$this->relatedBlock()->customize_block(false);
    }

    /** @test */
    function guest_can_register_on_event(){
        $this->inContent()->register_on_event();
        $this->inHeader()->register_on_event();
        // make no sense with related block. Solution needs custom implementation and testing inside blade file
        // $this->relatedBlock()->get_events_from_the_current_category();
    }

    /** @test */
    function guest_can_register_on_event_without_comment(){
        $this->inContent()->register_on_event_without_comment();
        $this->inHeader()->register_on_event_without_comment();
        //$this->relatedBlock()->register_on_event_without_comment();
    }

    /** @test */
    function guest_cannot_register_on_event_without_name(){
        $this->inContent()->cannot_register_on_event_without_name();
        $this->inHeader()->cannot_register_on_event_without_name();
        //$this->relatedBlock()->cannot_register_on_event_without_name();
    }

    /** @test */
    function guest_cannot_register_on_event_without_email(){
        $this->inContent()->cannot_register_on_event_without_email();
        $this->inHeader()->cannot_register_on_event_without_email();
        //$this->relatedBlock()->cannot_register_on_event_without_email();
    }

    /** @test */
    function guest_cannot_register_on_event_without_captcha(){
        $this->inContent()->cannot_register_on_event_without_captcha();
        $this->inHeader()->cannot_register_on_event_without_captcha();
        //$this->relatedBlock()->cannot_register_on_event_without_captcha();
    }

    /** @test */
    function guest_cannot_register_on_event_twice(){
        $this->inContent()->cannot_register_on_event_twice();
        $this->inHeader()->cannot_register_on_event_twice();
        //$this->relatedBlock()->cannot_register_on_event_twice();
    }

    /** @test */
    function admin_is_notified_about_guests_registration_in_event(){
        $this->inContent()->admin_is_notified_about_new_registration_on_event();
        $this->inHeader()->admin_is_notified_about_new_registration_on_event();
        //$this->relatedBlock()->admin_is_notified_about_new_registration_on_event();
    }
}
