<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Twitter;

class GuestAccessTest extends Actions
{
    
    /** @test*/
    function guest_cannot_access_empty_item_create_form(){
        $this->access_item_form(false);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->edit_block_settings(false);
    }
}
