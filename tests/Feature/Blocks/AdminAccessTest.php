<?php

namespace Lubart\Just\Tests\Feature\Blocks;

use Lubart\Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test */
    function admin_can_create_new_block(){
        $this->create_new_block(true);
    }
    
    /** @test */
    function admin_can_change_blocks_order(){
        $this->change_blocks_order(true);
    }
    
    /** @test */
    function admin_can_deactivate_block(){
        $this->deactivate_block(true);
    }
    
    /** @test */
    function admin_can_activate_block(){
        $this->activate_block(true);
    }
    
    /** @test */
    function admin_can_delete_block(){
        $this->delete_block(true);
    }
    
    /** @test */
    function admin_can_change_items_order(){
        $this->change_items_order_in_the_block(true);
    }
    
    /** @test */
    function admin_can_deactivate_item(){
        $this->deactivate_item_in_the_block(true);
    }
    
    /** @test */
    function admin_can_delete_item(){
        $this->delete_item_in_the_block(true);
    }
    
    /** @test */
    function admin_can_delete_image_together_with_item(){
        $this->delete_item_in_the_block_with_image(true);
    }
    
    /** @test */
    function admin_can_add_related_block(){
        $this->add_related_block_to_the_item(false);
    }
    
    /** @test */
    function admin_can_add_item_to_related_block(){
        $this->add_item_to_related_block(true);
    }
    
    /** @test */
    function admin_can_access_data_from_related_block(){
        $this->access_data_from_related_block(true);
    }
    
    /** @test */
    function admin_can_access_parent_block_from_the_related_one(){
        $this->access_parent_block_from_the_related_one(true);
    }
    
    /** @test */
    function admin_can_update_block_data(){
        $this->update_block_data(true);
    }
    
    /** @test */
    function admin_can_update_block_unique_name(){
        $this->update_block_unique_name(true);
    }
    
    /** @test */
    function admin_can_create_new_block_with_name(){
        $this->create_new_block_with_name(true);
    }
    
    /** @test */
    function admin_cannot_create_block_with_existing_name(){
        $this->cannot_create_block_with_existing_name();
    }
    
    /** @test */
    function admin_cannot_cannot_update_block_name_if_it_exists(){
        $this->cannot_update_block_name_if_it_exists();
    }
    
    /** @test */
    function admin_can_update_block_with_keeping_name_value(){
        $this->update_block_with_keeping_name_value(true);
    }

    /** @test */
    function admin_can_fetch_items_from_current_category(){
        $this->get_items_from_the_current_category();
    }

    /** @test */
    function admin_receive_null_on_empty_string_addon_value(){
        $this->get_nullable_value_on_empty_addon_string();
    }
}
