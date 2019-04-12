<?php

namespace Lubart\Just\Tests\Feature\Blocks;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test */
    function master_can_create_new_block(){
        $this->create_new_block(true);
    }
    
    /** @test */
    function master_can_change_blocks_order(){
        $this->change_blocks_order(true);
    }
    
    /** @test */
    function master_can_deactivate_block(){
        $this->deactivate_block(true);
    }
    
    /** @test */
    function master_can_activate_block(){
        $this->activate_block(true);
    }
    
    /** @test */
    function master_can_delete_block(){
        $this->delete_block(true);
    }
    
    /** @test */
    function master_can_change_items_order(){
        $this->change_items_order_in_the_block(true);
    }
    
    /** @test */
    function master_can_deactivate_item(){
        $this->deactivate_item_in_the_block(true);
    }
    
    /** @test */
    function master_can_delete_item(){
        $this->delete_item_in_the_block(true);
    }
    
    /** @test */
    function master_can_delete_image_together_with_item(){
        $this->delete_item_in_the_block_with_image(true);
    }
    
    /** @test */
    function master_can_add_related_block(){
        $this->add_related_block_to_the_item(true);
    }
    
    /** @test */
    function master_can_add_item_to_related_block(){
        $this->add_item_to_related_block(true);
    }
    
    /** @test */
    function master_can_access_parent_block_from_the_related_one(){
        $this->access_parent_block_from_the_related_one(true);
    }
    
    /** @test */
    function master_can_update_block_data(){
        $this->update_block_data(true);
    }
    
    /** @test */
    function master_can_update_block_unique_name(){
        $this->update_block_unique_name(true);
    }
    
    /** @test */
    function master_can_create_new_block_with_name(){
        $this->create_new_block_with_name(true);
    }
    
    /** @test */
    function master_cannot_create_block_with_existing_name(){
        $this->cannot_create_block_with_existing_name();
    }
    
    /** @test */
    function master_cannot_cannot_update_block_name_if_it_exists(){
        $this->cannot_update_block_name_if_it_exists();
    }
}
