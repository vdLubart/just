<?php

namespace Just\Tests\Feature\Blocks\ImageLibrary;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->actingAsMaster();
    }
    
    /** @test */
    function master_can_access_image_library(){
        $this->access_library(true);
    }
    
    /** @test */
    function master_can_upload_image_to_the_library(){
        $this->upload_image_to_the_library(true);
    }
}
