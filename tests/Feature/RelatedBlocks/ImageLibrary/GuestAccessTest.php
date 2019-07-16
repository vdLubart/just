<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\ImageLibrary;

class GuestAccessTest extends Actions
{
    /** @test */
    function guest_cannot_access_image_library(){
        $this->access_library(false);
    }
    
    /** @test */
    function guest_cannot_upload_image_to_the_library(){
        $this->upload_image_to_the_library(false);
    }
}
