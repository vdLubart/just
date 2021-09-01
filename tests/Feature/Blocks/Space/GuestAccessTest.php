<?php

namespace Just\Tests\Feature\Blocks\Space;

class GuestAccessTest extends Actions
{

    /** @test*/
    function guest_cannot_access_empty_item_create_form(){
        $this->inContent()->access_item_form(false);
        $this->inHeader()->access_item_form(false);
        //$this->relatedBlock()->access_item_form(false);
    }

    /** @test */
    function guest_cannot_edit_block_settings(){
        $this->inContent()->customize_block(false);
        $this->inHeader()->customize_block(false);
        //$this->relatedBlock()->customize_block(false);
    }
}
