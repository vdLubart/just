<?php

namespace Just\Tests\Feature\Addon;

class GuestAccessTest extends Actions
{
    /** @test - admin_cannot_access_create_addon_form */
    function guest_cannot_access_create_addon_form() {
        $this->access_create_addon_form(false);
    }

    /** @test */
    function guest_cannot_access_actions_settings_page(){
        $this->access_actions_settings_page(false);
    }

    /** @test */
    function guest_cannot_activate_addon(){
        $this->activate_addon(false);
    }

    /** @test */
    function guest_cannot_deactivate_addon(){
        $this->deactivate_addon(false);
    }

    /** @test */
    function guest_cannot_move_addon_up(){
        $this->move_addon_up(false);
    }

    /** @test */
    function guest_cannot_move_addon_down(){
        $this->move_addon_down(false);
    }

    /** @test */
    function guest_cannot_add_string_addon_to_the_block(){
        $this->add_phrase_addon_to_the_block(false);
    }

    /** @test */
    function guest_cannot_add_paragraph_addon_to_the_block(){
        $this->add_paragraph_addon_to_the_block(false);
    }

    /** @test */
    function guest_cannot_add_image_addon_to_the_block(){
        $this->add_image_addon_to_the_block(false);
    }

    /** @test */
    function guest_cannot_add_categories_addon_to_the_block(){
        $this->add_category_addon_to_the_block(false);
    }

    /** @test */
    function guest_cannot_add_tag_addon_to_the_block(){
        $this->add_tag_addon_to_the_block(false);
    }

    /** @test */
    function guest_cannot_edit_existing_string_addon(){
        $this->edit_existing_phrase_addon(false);
    }

    /** @test */
    function guest_cannot_edit_existing_paragraph_addon(){
        $this->edit_existing_paragraph_addon(false);
    }

    /** @test */
    function guest_cannot_edit_existing_image_addon(){
        $this->edit_existing_image_addon(false);
    }

    /** @test */
    function guest_cannot_edit_existing_categories_addon(){
        $this->edit_existing_category_addon(false);
    }

    /** @test */
    function guest_cannot_edit_existing_tag_addon(){
        $this->edit_existing_tag_addon(false);
    }

    /** @test */
    function guest_cannot_delete_existing_string_addon(){
        $this->delete_existing_phrase_addon(false);
    }

    /** @test */
    function guest_cannot_delete_existing_paragraph_addon(){
        $this->delete_existing_paragraph_addon(false);
    }

    /** @test */
    function guest_cannot_delete_existing_image_addon(){
        $this->delete_existing_image_addon(false);
    }

    /** @test */
    function guest_cannot_delete_existing_categories_addon(){
        $this->delete_existing_category_addon(false);
    }

    /** @test */
    function guest_cannot_delete_existing_tag_addon(){
        $this->delete_existing_tag_addon(false);
    }

    /** @test */
    function guest_cannot_create_item_with_string_addon_in_the_block(){
        $this->create_new_item_with_phrase_addon(false);
    }

    /** @test */
    function guest_cannot_create_item_with_paragraph_addon_in_the_block(){
        $this->create_new_item_with_paragraph_addon(false);
    }

    /** @test */
    function guest_cannot_create_item_with_image_addon_in_the_block(){
        $this->create_new_item_with_image_addon(false);
    }

    /** @test */
    function guest_cannot_create_item_with_categories_addon_in_the_block(){
        $this->create_new_item_with_category_addon(false);
    }

    /** @test */
    function guest_cannot_create_item_with_tag_addon_in_the_block(){
        $this->create_new_item_with_tag_addon(false);
    }

    /** @test */
    function guest_cannot_edit_item_with_string_addon_in_the_block(){
        $this->edit_item_with_phrase_addon(false);
    }

    /** @test */
    function guest_cannot_edit_item_with_paragraph_addon_in_the_block(){
        $this->edit_item_with_paragraph_addon(false);
    }

    /** @test */
    function guest_cannot_edit_item_with_image_addon_in_the_block(){
        $this->edit_item_with_image_addon(false);
    }

    /** @test */
    function guest_cannot_edit_item_with_categories_addon_in_the_block(){
        $this->edit_item_with_category_addon(false);
    }

    /** @test */
    function guest_cannot_edit_item_with_tag_addon_in_the_block(){
        $this->edit_item_with_tag_addon(false);
    }
}
