<?php

namespace Just\Tests\Feature\Blocks\Articles;

class GuestAccessTest extends Actions
{

    /** @test*/
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
}
