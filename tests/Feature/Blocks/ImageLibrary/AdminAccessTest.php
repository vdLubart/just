<?php

namespace Just\Tests\Feature\Blocks\ImageLibrary;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsAdmin();
    }
    
    /** @test */
    function admin_can_access_image_library(){
        $this->access_library(true);
    }
    
    /** @test */
    function admin_can_upload_image_to_the_library(){
        $this->upload_image_to_the_library(true);
    }
}
