<?php

namespace Lubart\Just\Tests\Feature\Addon;

use Lubart\Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test */
    function admin_cannot_add_string_addon_to_the_block(){
        $this->add_string_addon_to_the_block(false);
    }
    
    /** @test */
    function admin_cannot_add_paragraph_addon_to_the_block(){
        $this->add_paragraph_addon_to_the_block(false);
    }
    
    /** @test */
    function admin_cannot_add_image_addon_to_the_block(){
        $this->add_image_addon_to_the_block(false);
    }
    
    /** @test */
    function admin_cannot_add_categories_addon_to_the_block(){
        $this->add_categories_addon_to_the_block(false);
    }
    
    /** @test */
    function admin_cannot_edit_existing_string_addon(){
        $this->edit_existing_string_addon(false);
    }
    
    /** @test */
    function admin_cannot_edit_existing_paragraph_addon(){
        $this->edit_existing_paragraph_addon(false);
    }
    
    /** @test */
    function admin_cannot_edit_existing_image_addon(){
        $this->edit_existing_image_addon(false);
    }
    
    /** @test */
    function admin_cannot_edit_existing_categories_addon(){
        $this->edit_existing_categories_addon(false);
    }
    
    /** @test */
    function admin_cannot_delete_existing_string_addon(){
        $this->delete_existing_string_addon(false);
    }
    
    /** @test */
    function admin_cannot_delete_existing_paragraph_addon(){
        $this->delete_existing_paragraph_addon(false);
    }
    
    /** @test */
    function admin_cannot_delete_existing_image_addon(){
        $this->delete_existing_image_addon(false);
    }
    
    /** @test */
    function admin_cannot_delete_existing_categories_addon(){
        $this->delete_existing_categories_addon(false);
    }
    
    /** @test */
    function admin_can_delete_existing_category_value(){
        $this->delete_existing_category_value(true);
    }
    
    /** @test */
    function admin_can_create_item_with_string_addon_in_the_block(){
        $this->create_new_item_with_string_addon(true);
    }
    
    /** @test */
    function admin_can_create_item_with_paragraph_addon_in_the_block(){
        $this->create_new_item_with_paragraph_addon(true);
    }
    
    /** @test */
    function admin_can_create_item_with_image_addon_in_the_block(){
        $this->create_new_item_with_image_addon(true);
    }
    
    /** @test */
    function admin_can_create_item_with_categories_addon_in_the_block(){
        $this->create_new_item_with_categories_addon(true);
    }
    
    /** @test */
    function admin_can_edit_item_with_string_addon_in_the_block(){
        $this->edit_item_with_string_addon(true);
    }
    
    /** @test */
    function admin_can_edit_item_with_paragraph_addon_in_the_block(){
        $this->edit_item_with_paragraph_addon(true);
    }
    
    /** @test */
    function admin_can_edit_item_with_image_addon_in_the_block(){
        $this->edit_item_with_image_addon(true);
    }
    
    /** @test */
    function admin_can_edit_item_with_categories_addon_in_the_block(){
        $this->edit_item_with_categories_addon(true);
    }
}
