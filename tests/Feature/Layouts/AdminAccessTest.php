<?php

namespace Just\Tests\Feature\Layouts;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test */
    function admin_cannot_change_default_layout(){
        $this->cannot_change_default_layout();
    }
    
    /** @test */
    function admin_cannot_create_new_layout(){
        $this->create_new_layout(false);
    }
    
    /** @test */
    function admin_cannot_create_layout_with_existing_class(){
        $this->cannot_create_layout_with_existing_class();
    }
    
    /** @test */
    function admin_cannot_choose_default_layout(){
        $this->choose_default_layout(false);
    }
    
    /** @test */
    function admin_cannot_access_layout_list(){
        $this->access_layout_list(false);
    }
    
    /** @test */
    function admin_cannot_delete_layout(){
        $this->delete_layout(false);
    }

    /** @test */
    function admin_can_get_layout_from_the_panel(){
        $this->get_layout_from_the_panel();
    }
}
