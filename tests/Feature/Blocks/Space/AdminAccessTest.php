<?php

namespace Just\Tests\Feature\Blocks\Space;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test*/
    function admin_can_access_empty_item_create_form(){
        $this->inContent()->access_item_form(true);
        $this->inHeader()->access_item_form(true);
        //$this->relatedBlock()->access_item_form(true);
    }
    
    /** @test */
    function admin_can_edit_block_settings(){
        $this->inContent()->edit_block_settings(true);
        $this->inHeader()->edit_block_settings(true);
        //$this->relatedBlock()->edit_block_settings(true);
    }
}
