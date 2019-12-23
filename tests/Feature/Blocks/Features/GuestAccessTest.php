<?php

namespace Just\Tests\Feature\Blocks\Features;

class GuestAccessTest extends Actions
{
    
    /** @test*/
    function guest_cannot_access_item_create_form(){
        $this->inContent()->access_item_form_when_block_is_setted_up(false);
        $this->inHeader()->access_item_form_when_block_is_setted_up(false);
        $this->relatedBlock()->access_item_form_when_block_is_setted_up(false);
    }
    
    /** @test */
    function guest_cannot_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(false);
        $this->inHeader()->access_edit_item_form(false);
        $this->relatedBlock()->access_edit_item_form(false);
    }
    
    /** @test */
    function guest_cannot_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(false);
        $this->inHeader()->create_new_item_in_block(false);
        $this->relatedBlock()->create_new_item_in_block(false);
    }
    
    /** @test */
    function guest_cannot_recieve_an_error_on_sending_incompleate_create_item_form(){
        $this->inContent()->receive_an_error_on_sending_incompleate_create_item_form(false);
        $this->inHeader()->receive_an_error_on_sending_incompleate_create_item_form(false);
        $this->relatedBlock()->receive_an_error_on_sending_incompleate_create_item_form(false);
    }
    
    /** @test */
    function guest_cannot_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(false);
        $this->inHeader()->edit_existing_item_in_the_block(false);
        $this->relatedBlock()->edit_existing_item_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->inContent()->edit_block_settings(false);
        $this->inHeader()->edit_block_settings(false);
        $this->relatedBlock()->edit_block_settings(false);
    }
}
