<?php

namespace Just\Tests\Feature\Addon;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test - admin_cannot_access_create_addon_form */
    function master_can_access_create_addon_form() {
        $this->access_create_addon_form(true);
    }

    /** @test */
    function master_can_add_string_addon_to_the_block(){
        $this->add_phrase_addon_to_the_block(true);
    }

    /** @test */
    function master_can_add_paragraph_addon_to_the_block(){
        $this->add_paragraph_addon_to_the_block(true);
    }

    /** @test */
    function master_can_add_image_addon_to_the_block(){
        $this->add_image_addon_to_the_block(true);
    }

    /** @test */
    function master_can_add_categories_addon_to_the_block(){
        $this->add_category_addon_to_the_block(true);
    }

    /** @test */
    function master_can_edit_existing_string_addon(){
        $this->edit_existing_phrase_addon(true);
    }

    /** @test */
    function master_can_edit_existing_paragraph_addon(){
        $this->edit_existing_paragraph_addon(true);
    }

    /** @test */
    function master_can_edit_existing_image_addon(){
        $this->edit_existing_image_addon(true);
    }

    /** @test */
    function master_can_edit_existing_categories_addon(){
        $this->edit_existing_category_addon(true);
    }

    /** @test */
    function master_can_delete_existing_string_addon(){
        $this->delete_existing_phrase_addon(true);
    }

    /** @test */
    function master_can_delete_existing_paragraph_addon(){
        $this->delete_existing_paragraph_addon(true);
    }

    /** @test */
    function master_can_delete_existing_image_addon(){
        $this->delete_existing_image_addon(true);
    }

    /** @test */
    function master_can_delete_existing_categories_addon(){
        $this->delete_existing_category_addon(true);
    }

    /** @test */
    function master_can_create_item_with_string_addon_in_the_block(){
        $this->create_new_item_with_phrase_addon(true);
    }

    /** @test */
    function master_can_create_item_with_paragraph_addon_in_the_block(){
        $this->create_new_item_with_paragraph_addon(true);
    }

    /** @test */
    function master_can_create_item_with_image_addon_in_the_block(){
        $this->create_new_item_with_image_addon(true);
    }

    /** @test */
    function master_can_create_item_with_categories_addon_in_the_block(){
        $this->create_new_item_with_category_addon(true);
    }

    /** @test */
    function master_can_edit_item_with_string_addon_in_the_block(){
        $this->edit_item_with_phrase_addon(true);
    }

    /** @test */
    function master_can_edit_item_with_paragraph_addon_in_the_block(){
        $this->edit_item_with_paragraph_addon(true);
    }

    /** @test */
    function master_can_edit_item_with_image_addon_in_the_block(){
        $this->edit_item_with_image_addon(true);
    }

    /** @test */
    function master_can_edit_item_with_categories_addon_in_the_block(){
        $this->edit_item_with_category_addon(true);
    }
}
