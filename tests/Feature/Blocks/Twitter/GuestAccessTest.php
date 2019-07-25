<?php

namespace Lubart\Just\Tests\Feature\Blocks\Twitter;

class GuestAccessTest extends Actions
{
    
    /** @test*/
    function guest_cannot_access_empty_item_create_form(){
        $this->inContent()->access_item_form(false);
		$this->inHeader()->access_item_form(false);
		//$this->relatedBlock()->access_item_form(false);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->inContent()->edit_block_settings(false);
		$this->inHeader()->edit_block_settings(false);
		$this->relatedBlock()->edit_block_settings(false);
    }
}
