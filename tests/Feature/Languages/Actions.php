<?php
namespace Just\Tests\Feature\Languages;

use Tests\TestCase;

class Actions extends TestCase {

    function access_language_list($assertion) {
        $this->addWarning('Language module is not ready');
        /*
        $response = $this->get('admin/settings/lang/list');
        if($assertion){
            $response->assertSuccessful()
                ->assertSee("Settings :: Languages");
        }
        else{
            $response->assertRedirect();
        }
        */
    }

}
