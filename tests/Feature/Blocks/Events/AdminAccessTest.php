<?php

namespace Just\Tests\Feature\Blocks\Events;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test */
    function admin_can_access_item_form(){
        $this->inContent()->access_item_form(true);
        $this->inHeader()->access_item_form(true);
        //$this->relatedBlock()->access_item_form(true);
    }

    /** @test */
    function admin_can_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(true);
        $this->inHeader()->access_edit_item_form(true);
        //$this->relatedBlock()->access_edit_item_form(true);
    }

    /** @test */
    function admin_can_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(true);
        $this->inHeader()->create_new_item_in_block(true);
        //$this->relatedBlock()->create_new_item_in_block(true);
    }

    /** @test */
    function admin_receives_an_error_on_sending_incomplete_create_item_form(){
        $this->inContent()->receive_an_error_on_sending_incomplete_create_item_form(true);
        $this->inHeader()->receive_an_error_on_sending_incomplete_create_item_form(true);
        //$this->relatedBlock()->receive_an_error_on_sending_incomplete_create_item_form(true);
    }

    /** @test */
    function admin_receive_an_error_on_wrong_date_and_time_format(){
        $this->inContent()->receive_an_error_on_wrong_date_and_time_format(true);
        $this->inHeader()->receive_an_error_on_wrong_date_and_time_format(true);
        //$this->relatedBlock()->receive_an_error_on_wrong_date_and_time_format(true);
    }

    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
        $this->inHeader()->edit_existing_item_in_the_block(true);
        //$this->relatedBlock()->edit_existing_item_in_the_block(true);
    }

    /** @test */
    function admin_can_access_created_item(){
        $this->inContent()->access_created_item(true);
        $this->inHeader()->access_created_item(true);
        //$this->relatedBlock()->access_created_item(true);
    }

    /** @test */
    function admin_can_customize_block(){
        $this->inContent()->customize_block(true);
        $this->inHeader()->customize_block(true);
        //$this->relatedBlock()->customize_block(true);
    }

    /** @test */
    function admin_can_create_item_with_standard_image_sizes(){
        $this->inContent()->create_item_with_standard_image_sizes();
        $this->inHeader()->create_item_with_standard_image_sizes();
        //$this->relatedBlock()->create_item_with_standard_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_custom_image_sizes(){
        $this->inContent()->create_item_with_custom_image_sizes();
        $this->inHeader()->create_item_with_custom_image_sizes();
        //$this->relatedBlock()->create_item_with_custom_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_empty_custom_image_sizes(){
        $this->inContent()->create_item_with_empty_custom_image_sizes();
        $this->inHeader()->create_item_with_empty_custom_image_sizes();
        //$this->relatedBlock()->create_item_with_empty_custom_image_sizes();
    }

    /** @test */
    function admin_can_register_on_event(){
        $this->inContent()->register_on_event();
        $this->inHeader()->register_on_event();
        //$this->relatedBlock()->register_on_event();
    }

    /** @test */
    function admin_can_register_on_event_without_comment(){
        $this->inContent()->register_on_event_without_comment();
        $this->inHeader()->register_on_event_without_comment();
        //$this->relatedBlock()->register_on_event_without_comment();
    }

    /** @test */
    function admin_cannot_register_on_event_without_name(){
        $this->inContent()->cannot_register_on_event_without_name();
        $this->inHeader()->cannot_register_on_event_without_name();
        //$this->relatedBlock()->cannot_register_on_event_without_name();
    }

    /** @test */
    function admin_cannot_register_on_event_without_email(){
        $this->inContent()->cannot_register_on_event_without_email();
        $this->inHeader()->cannot_register_on_event_without_email();
        //$this->relatedBlock()->cannot_register_on_event_without_email();
    }

    /** @test */
    function admin_cannot_register_on_event_without_captcha(){
        $this->inContent()->cannot_register_on_event_without_captcha();
        $this->inHeader()->cannot_register_on_event_without_captcha();
        //$this->relatedBlock()->cannot_register_on_event_without_captcha();
    }

    /** @test */
    function admin_cannot_register_on_event_twice(){
        $this->inContent()->cannot_register_on_event_twice();
        $this->inHeader()->cannot_register_on_event_twice();
        //$this->relatedBlock()->cannot_register_on_event_twice();
    }

    /** @test */
    function admin_is_notified_about_admins_registration_in_event(){
        $this->inContent()->admin_is_notified_about_new_registration_on_event();
        $this->inHeader()->admin_is_notified_about_new_registration_on_event();
        //$this->relatedBlock()->admin_is_notified_about_new_registration_on_event();
    }

    /** @test */
    function admin_can_see_list_of_registered_users(){
        $this->inContent()->user_can_see_list_of_registered_users();
        $this->inHeader()->user_can_see_list_of_registered_users();
        //$this->relatedBlock()->user_can_see_list_of_registered_users();
    }
}
