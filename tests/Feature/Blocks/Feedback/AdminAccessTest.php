<?php

namespace Just\Tests\Feature\Blocks\Feedback;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test*/
    function admin_can_access_item_create_form(){
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
    function admin_can_leave_feedback_from_the_public_side(){
        $this->inContent()->leave_feedback_from_the_website(true);
        $this->inHeader()->leave_feedback_from_the_website(true);
        //$this->relatedBlock()->leave_feedback_from_the_website(true);
    }

    /** @test */
    function admin_cannot_leave_feedback_if_block_does_not_exist_any_more(){
        $this->inContent()->redirect_back_if_feedback_form_does_not_exist_any_more();
    }

    /** @test */
    function admin_recieves_an_error_on_sending_incompleate_feedback_on_the_website(){
        $this->inContent()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
        $this->inHeader()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
        //$this->relatedBlock()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
    }

    /** @test */
    function admin_can_create_few_items_in_the_block(){
        $this->inContent()->create_few_items_in_block(true);
        $this->inHeader()->create_few_items_in_block(true);
        //$this->relatedBlock()->create_few_items_in_block(true);
    }

    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
        $this->inHeader()->edit_existing_item_in_the_block(true);
        //$this->relatedBlock()->edit_existing_item_in_the_block(true);
    }

    /** @test */
    function admin_can_customize_block(){
        $this->inContent()->customize_block(true);
        $this->inHeader()->customize_block(true);
        //$this->relatedBlock()->customize_block(true);
    }
}
