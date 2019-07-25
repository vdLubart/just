<?php

namespace Lubart\Just\Tests\Feature\Blocks\Slider;

class GuestAccessTest extends Actions
{
    
    /** @test*/
    function guest_cannot_access_item_form(){
        $this->inContent()->access_item_form(false);
		$this->inHeader()->access_item_form(false);
		$this->relatedBlock()->access_item_form(false);
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
    function guest_cannot_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(false);
		$this->inHeader()->edit_existing_item_in_the_block(false);
		$this->relatedBlock()->edit_existing_item_in_the_block(false);
    }
    
    /** @test */
    function guest_cannot_crop_photo(){
        $this->inContent()->crop_photo(false);
		$this->inHeader()->crop_photo(false);
		$this->relatedBlock()->crop_photo(false);
    }
    
    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->inContent()->edit_block_settings(false);
		$this->inHeader()->edit_block_settings(false);
		$this->relatedBlock()->edit_block_settings(false);
    }
}
