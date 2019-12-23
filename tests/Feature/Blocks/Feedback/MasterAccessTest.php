<?php

namespace Just\Tests\Feature\Blocks\Feedback;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test*/
    function master_can_access_item_create_form(){
        $this->inContent()->access_item_form(true);
        $this->inHeader()->access_item_form(true);
        $this->relatedBlock()->access_item_form(true);
    }
    
    /** @test */
    function master_can_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(true);
        $this->inHeader()->access_edit_item_form(true);
        $this->relatedBlock()->access_edit_item_form(true);
    }
    
    /** @test */
    function master_can_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(true);
        $this->inHeader()->create_new_item_in_block(true);
        $this->relatedBlock()->create_new_item_in_block(true);
    }
    
    /** @test */
    function master_recieves_an_error_on_sending_incompleate_create_item_form(){
        $this->inContent()->receive_an_error_on_sending_incompleate_create_item_form(true);
        $this->inHeader()->receive_an_error_on_sending_incompleate_create_item_form(true);
        $this->relatedBlock()->receive_an_error_on_sending_incompleate_create_item_form(true);
    }
    
    /** @test */
    function master_can_leave_feedback_from_the_public_side(){
        $this->inContent()->leave_feedback_from_the_website(true);
        $this->inHeader()->leave_feedback_from_the_website(true);
        $this->relatedBlock()->leave_feedback_from_the_website(true);
    }
    
    /** @test */
    function master_recieves_an_error_on_sending_incompleate_feedback_on_the_website(){
        $this->inContent()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
        $this->inHeader()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
        $this->relatedBlock()->receive_an_error_on_sending_incompleate_feedback_on_the_website();
    }
    
    /** @test */
    function master_can_create_few_items_in_the_block(){
        $this->inContent()->create_few_items_in_block(true);
        $this->inHeader()->create_few_items_in_block(true);
        $this->relatedBlock()->create_few_items_in_block(true);
    }
    
    /** @test */
    function master_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
        $this->inHeader()->edit_existing_item_in_the_block(true);
        $this->relatedBlock()->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function master_can_edit_block_settings(){
        $this->inContent()->edit_block_settings(true);
        $this->inHeader()->edit_block_settings(true);
        $this->relatedBlock()->edit_block_settings(true);
    }
}
