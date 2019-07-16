<?php

namespace Lubart\Just\Tests\Feature\Blocks\Contact;

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
    function admin_doesnt_recieve_an_error_on_sending_incompleate_create_item_form(){
        $this->inContent()->dont_receive_an_error_on_sending_incompleate_create_item_form(true);
		$this->inHeader()->dont_receive_an_error_on_sending_incompleate_create_item_form(true);
		$this->relatedBlock()->dont_receive_an_error_on_sending_incompleate_create_item_form(true);
    }
    
    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->inContent()->edit_existing_item_in_the_block(true);
		$this->inHeader()->edit_existing_item_in_the_block(true);
		$this->relatedBlock()->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function admin_can_edit_block_settings(){
        $this->inContent()->edit_block_settings(true);
		$this->inHeader()->edit_block_settings(true);
		$this->relatedBlock()->edit_block_settings(true);
    }

    /** @test */
    function admin_cannot_add_custom_contact_channel(){
        $this->inContent()->add_custom_contact_channel(false);
		$this->inHeader()->add_custom_contact_channel(false);
		$this->relatedBlock()->add_custom_contact_channel(false);
    }

    /** @test */
    function admin_can_change_contact_channels(){
        $this->inContent()->change_contact_channels(true);
		$this->inHeader()->change_contact_channels(true);
		$this->relatedBlock()->change_contact_channels(true);
    }
}
