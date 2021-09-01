<?php

namespace Just\Tests\Feature\Blocks\Contact;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test*/
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
    function admin_can_create_item_with_a_lot_of_data(){
        $this->inContent()->create_new_item_with_a_lot_of_data(true);
        $this->inHeader()->create_new_item_with_a_lot_of_data(true);
        //$this->relatedBlock()->create_new_item_with_a_lot_of_data(true);
    }

    /** @test */
    function admin_receives_errors_on_create_item_with_wrong_data(){
        $this->inContent()->receive_errors_on_creating_item_with_wrong_data();
        $this->inHeader()->receive_errors_on_creating_item_with_wrong_data();
        //$this->relatedBlock()->receive_errors_on_creating_item_with_wrong_data();
    }

    /** @test */
    function admin_doesnt_receive_an_error_on_sending_incomplete_create_item_form(){
        $this->inContent()->dont_receive_an_error_on_sending_incomplete_create_item_form(true);
        $this->inHeader()->dont_receive_an_error_on_sending_incomplete_create_item_form(true);
        //$this->relatedBlock()->dont_receive_an_error_on_sending_incomplete_create_item_form(true);
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

    /** @test */
    function admin_cannot_add_custom_contact_channel(){
        $this->inContent()->add_custom_contact_channel(false);
        $this->inHeader()->add_custom_contact_channel(false);
        //$this->relatedBlock()->add_custom_contact_channel(false);
    }

    /** @test */
    function admin_cannot_add_few_custom_contact_channels(){
        $this->inContent()->add_few_custom_contact_channels(false);
        $this->inHeader()->add_few_custom_contact_channels(false);
        //$this->relatedBlock()->add_few_custom_contact_channels(false);
    }

    /** @test */
    function admin_can_change_contact_channels(){
        $this->inContent()->change_contact_channels(true);
        $this->inHeader()->change_contact_channels(true);
        //$this->relatedBlock()->change_contact_channels(true);
    }
}
