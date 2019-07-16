<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Gallery;

class GuestAccessTest extends Actions
{
    
    /** @test*/
    function guest_cannot_access_item_form(){
        $this->access_item_form(false);
    }
    
    /** @test */
    function guest_cannot_access_item_edit_form(){
        $this->access_edit_item_form(false);
    }
    
    /** @test */
    function guest_cannot_create_item_in_the_block(){
        $this->create_new_item_in_block(false);
    }
    
    /** @test */
    function guest_cannot_edit_item_in_the_block(){
        $this->edit_existing_item_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_crop_photo(){
        $this->crop_photo(false);
    }

    /** @test */
    function guest_cannot_recrop_photo(){
        $this->recrop_photo(false);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->edit_block_settings(false);
    }
}
