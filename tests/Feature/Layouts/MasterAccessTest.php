<?php

namespace Just\Tests\Feature\Layouts;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test */
    function master_cannot_change_default_layout(){
        $this->cannot_change_default_layout();
    }
    
    /** @test */
    function master_can_create_new_layout(){
        $this->create_new_layout(true);
    }
    
    /** @test */
    function master_cannot_create_layout_with_existing_class(){
        $this->cannot_create_layout_with_existing_class();
    }
    
    /** @test */
    function master_can_choose_default_layout(){
        $this->choose_default_layout(true);
    }
    
    /** @test */
    function master_can_access_layout_list(){
        $this->access_layout_list(true);
    }
    
    /** @test */
    function master_can_delete_layout(){
        $this->delete_layout(true);
    }

    /** @test */
    function master_can_get_layout_from_the_panel(){
        $this->get_layout_from_the_panel();
    }
}
