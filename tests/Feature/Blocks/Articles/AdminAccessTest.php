<?php

namespace Just\Tests\Feature\Blocks\Articles;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test*/
    function admin_can_access_item_form_when_block_is_setted_up(){
        $this->inContent()->access_item_form(true);
        $this->inHeader()->access_item_form(true);
        //$this->relatedBlock()->access_item_form(true);
    }

    /** @test */
    function admin_can_access_item_edit_form(){
        $this->inContent()->access_edit_item_form(true);
        $this->inHeader()->access_edit_item_form(true);
        //$this->relatedBlock()->access_edit_item_form(true);
    }

    /** @test */
    function admin_can_create_item_in_the_block(){
        $this->inContent()->create_new_item_in_block(true);
        $this->inHeader()->create_new_item_in_block(true);
        //$this->relatedBlock()->create_new_item_in_block(true);
    }

    /** @test */
    function admin_can_create_item_in_the_block_without_cropping(){
        $this->inContent()->create_new_item_in_block_without_cropping_image();
        $this->inHeader()->create_new_item_in_block_without_cropping_image();
        //$this->relatedBlock()->create_new_item_in_block_without_cropping_image();
    }

    /** @test */
    function admin_receives_an_error_on_sending_incomplete_create_item_form(){
        $this->inContent()->receive_an_error_on_sending_incomplete_create_item_form(true);
        $this->inHeader()->receive_an_error_on_sending_incomplete_create_item_form(true);
        //$this->relatedBlock()->receive_an_error_on_sending_incomplete_create_item_form(true);
    }

    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
        $this->inHeader()->edit_existing_item_in_the_block(true);
        //$this->relatedBlock()->edit_existing_item_in_the_block(true);
    }

    /** @test */
    function admin_can_access_created_item(){
        $this->inContent()->access_created_item(true);
        $this->inHeader()->access_created_item(true);
        //$this->relatedBlock()->access_created_item(true);
    }

    /** @test */
    function admin_can_customize_block(){
        $this->inContent()->customize_block(true);
        $this->inHeader()->customize_block(true);
        //$this->relatedBlock()->customize_block(true);
    }

    /** @test */
    function admin_can_create_item_with_standard_image_sizes(){
        $this->inContent()->create_item_with_standard_image_sizes();
        $this->inHeader()->create_item_with_standard_image_sizes();
        //$this->relatedBlock()->create_item_with_standard_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_custom_image_sizes(){
        $this->inContent()->create_item_with_custom_image_sizes();
        $this->inHeader()->create_item_with_custom_image_sizes();
        //$this->relatedBlock()->create_item_with_custom_image_sizes();
    }

    /** @test */
    function admin_can_create_item_with_empty_custom_image_sizes(){
        $this->inContent()->create_item_with_empty_custom_image_sizes();
        $this->inHeader()->create_item_with_empty_custom_image_sizes();
        //$this->relatedBlock()->create_item_with_empty_custom_image_sizes();
    }
}
