<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Html;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test*/
    function master_can_access_item_create_form(){
        $this->access_item_form(true);
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
    function master_recieves_an_error_on_sending_incompleate_create_item_form(){
        $this->receive_an_error_on_sending_incompleate_create_item_form(true);
    }
    
    /** @test */
    function master_can_create_few_items_in_the_block(){
        $this->create_few_items_in_block(true);
    }
    
    /** @test */
    function master_can_edit_item_in_the_block(){
        $this->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function master_can_edit_block_settings(){
        $this->edit_block_settings(true);
    }
}
