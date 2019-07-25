<?php

namespace Lubart\Just\Tests\Feature\Blocks\Menu;

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
    function master_can_create_item_with_link_to_another_page(){
        $this->inContent()->create_new_item_with_link_to_another_page(true);
		$this->inHeader()->create_new_item_with_link_to_another_page(true);
		$this->relatedBlock()->create_new_item_with_link_to_another_page(true);
    }
    
    /** @test */
    function master_can_create_item_with_custom_url(){
        $this->inContent()->create_new_item_with_custom_url(true);
		$this->inHeader()->create_new_item_with_custom_url(true);
		$this->relatedBlock()->create_new_item_with_custom_url(true);
    }
    
    /** @test */
    function master_can_create_few_items_in_the_block(){
        $this->inContent()->create_few_items_in_block(true);
		$this->inHeader()->create_few_items_in_block(true);
		//$this->relatedBlock()->create_few_items_in_block(true);
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

    /** @test */
    function master_can_change_item_order_in_the_menu_list(){
        $this->inContent()->change_items_order_in_the_block(true);
        $this->inHeader()->change_items_order_in_the_block(true);
        $this->relatedBlock()->change_items_order_in_the_block(true);
    }
}
