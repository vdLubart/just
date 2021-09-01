<?php

namespace Just\Tests\Feature\Blocks\Space;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions
{
    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test*/
    function master_can_access_empty_item_create_form(){
        $this->inContent()->access_item_form(true);
        $this->inHeader()->access_item_form(true);
        //$this->relatedBlock()->access_item_form(true);
    }

    /** @test */
    function master_can_edit_block_settings(){
        $this->inContent()->customize_block(true);
        $this->inHeader()->customize_block(true);
        //$this->relatedBlock()->customize_block(true);
    }
}
