<?php

namespace Lubart\Just\Tests\Feature\Blocks\Gallery;

use Lubart\Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test*/
    function admin_can_access_item_form(){
        $this->inContent()->access_item_form(true);
		$this->inHeader()->access_item_form(true);
		$this->relatedBlock()->access_item_form(true);
    }
    
    /** @test */
    function admin_can_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(true);
		$this->inHeader()->access_edit_item_form(true);
		$this->relatedBlock()->access_edit_item_form(true);
    }

    /** @test */
    function admin_can_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(true);
		$this->inHeader()->create_new_item_in_block(true);
		$this->relatedBlock()->create_new_item_in_block(true);
    }
    
    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
		$this->inHeader()->edit_existing_item_in_the_block(true);
		$this->relatedBlock()->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function admin_can_crop_photo(){
        $this->inContent()->crop_photo(true);
		$this->inHeader()->crop_photo(true);
		$this->relatedBlock()->crop_photo(true);
    }

    /** @test */
    function admin_can_recrop_photo(){
        $this->inContent()->recrop_photo(true);
		$this->inHeader()->recrop_photo(true);
		$this->relatedBlock()->recrop_photo(true);
    }
    
    /** @test */
    function admin_can_edit_block_settings(){
        $this->inContent()->edit_block_settings(true);
		$this->inHeader()->edit_block_settings(true);
		$this->relatedBlock()->edit_block_settings(true);
    }

    /** @test */
    function admin_can_create_item_with_standard_image_sizes(){
        $this->inContent()->create_item_with_standard_image_sizes();
		$this->inHeader()->create_item_with_standard_image_sizes();
		$this->relatedBlock()->create_item_with_standard_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_custom_image_sizes(){
        $this->inContent()->create_item_with_custom_image_sizes();
		$this->inHeader()->create_item_with_custom_image_sizes();
		$this->relatedBlock()->create_item_with_custom_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_empty_custom_image_sizes(){
        $this->inContent()->create_item_with_empty_custom_image_sizes();
		$this->inHeader()->create_item_with_empty_custom_image_sizes();
		$this->relatedBlock()->create_item_with_empty_custom_image_sizes();
    }
}
