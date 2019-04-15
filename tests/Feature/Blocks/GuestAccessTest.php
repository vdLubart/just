<?php

namespace Lubart\Just\Tests\Feature\Blocks;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_create_new_block(){
        $this->create_new_block(false);
    }
    
    /** @test */
    function guest_cannot_change_blocks_order(){
        $this->change_blocks_order(false);
    }
    
    /** @test */
    function guest_cannot_change_items_order(){
        $this->change_items_order_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_deactivate_block(){
        $this->deactivate_block(false);
    }
    
    /** @test */
    function guest_cannot_activate_block(){
        $this->activate_block(false);
    }
    
    /** @test */
    function guest_cannot_deactivate_item(){
        $this->deactivate_item_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_delete_item(){
        $this->delete_item_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_delete_block(){
        $this->delete_block(false);
    }
    
    /** @test */
    function guest_cannot_delete_image_together_with_item(){
        $this->delete_item_in_the_block_with_image(false);
    }
    
    /** @test */
    function guest_cannot_add_related_block(){
        $this->add_related_block_to_the_item(false);
    }
    
    /** @test */
    function guest_cannot_add_item_to_related_block(){
        $this->add_item_to_related_block(false);
    }
    
    /** @test */
    function guest_can_access_parent_block_from_the_related_one(){
        $this->access_parent_block_from_the_related_one(true);
    }
    
    /** @test */
    function guest_cannot_update_block_data(){
        $this->update_block_data(false);
    }
    
    /** @test */
    function guest_cannot_update_block_unique_name(){
        $this->update_block_unique_name(false);
    }
    
    /** @test */
    function guest_cannot_create_new_block_with_name(){
        $this->create_new_block_with_name(false);
    }
    
    /** @test */
    function guest_cannot_create_block_with_existing_name(){
        $this->cannot_create_block_with_existing_name();
    }
    
    /** @test */
    function guest_cannot_update_block_with_keeping_name_value(){
        $this->update_block_with_keeping_name_value(false);
    }
}
