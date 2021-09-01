<?php

namespace Just\Tests\Feature\Blocks\Contact;

class GuestAccessTest extends Actions
{

    /** @test */
    function guest_cannot_access(){
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
    function guest_cannot_create_item_with_a_lot_of_data(){
        $this->inContent()->create_new_item_with_a_lot_of_data(false);
        $this->inHeader()->create_new_item_with_a_lot_of_data(false);
        //$this->relatedBlock()->create_new_item_with_a_lot_of_data(false);
    }

    /** @test */
    function guest_cannot_receive_an_error_on_sending_incomplete_create_item_form(){
        $this->inContent()->dont_receive_an_error_on_sending_incomplete_create_item_form(false);
        $this->inHeader()->dont_receive_an_error_on_sending_incomplete_create_item_form(false);
        //$this->relatedBlock()->dont_receive_an_error_on_sending_incomplete_create_item_form(false);
    }

    /** @test */
    function guest_cannot_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(false);
        $this->inHeader()->edit_existing_item_in_the_block(false);
        //$this->relatedBlock()->edit_existing_item_in_the_block(false);
    }

    /** @test */
    function guest_cannot_customize_block(){
        $this->inContent()->customize_block(false);
        $this->inHeader()->customize_block(false);
        //$this->relatedBlock()->customize_block(false);
    }

    /** @test */
    function guest_cannot_add_custom_contact_channel(){
        $this->inContent()->add_custom_contact_channel(false);
        $this->inHeader()->add_custom_contact_channel(false);
        //$this->relatedBlock()->add_custom_contact_channel(false);
    }

    /** @test */
    function guest_cannot_add_few_custom_contact_channels(){
        $this->inContent()->add_few_custom_contact_channels(false);
        $this->inHeader()->add_few_custom_contact_channels(false);
        //$this->relatedBlock()->add_few_custom_contact_channels(false);
    }

    /** @test */
    function guest_cannot_change_contact_channels(){
        $this->inContent()->change_contact_channels(false);
        $this->inHeader()->change_contact_channels(false);
        //$this->relatedBlock()->change_contact_channels(false);
    }
}
