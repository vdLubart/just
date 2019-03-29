<?php

namespace Lubart\Just\Tests\Feature\Blocks\Twitter;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test*/
    function master_can_access_empty_item_create_form(){
        $this->access_item_form(true);
    }
    
    /** @test */
    function master_can_edit_block_settings(){
        $this->edit_block_settings(true);
    }
}
