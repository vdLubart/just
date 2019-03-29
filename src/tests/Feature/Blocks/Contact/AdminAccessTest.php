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
        $this->access_item_form(true);
    }
    
    /** @test */
    function admin_can_access_item_edit_form(){
        $this->access_edit_item_form(true);
    }

    /** @test */
    function admin_can_create_item_in_the_block(){
        $this->create_new_item_in_block(true);
    }
    
    /** @test */
    function admin_doesnt_recieve_an_error_on_sending_incompleate_create_item_form(){
        $this->dont_receive_an_error_on_sending_incompleate_create_item_form(true);
    }
    
    /** @test */
    function admin_can_edit_item_in_the_block(){
        $this->edit_existing_item_in_the_block(true);
    }
    
    /** @test */
    function admin_can_edit_block_settings(){
        $this->edit_block_settings(true);
    }
}
