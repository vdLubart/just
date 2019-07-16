<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\ImageLibrary;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;
    
    public function setUp() {
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
