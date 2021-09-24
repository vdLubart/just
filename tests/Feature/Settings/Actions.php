<?php
namespace Just\Tests\Feature\Settings;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class Actions extends TestCase {

    function access_settings_home_page($assertion) {
        $response = $this->get('settings');

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(Auth::user()->role === 'master' ? 4 : 1, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect('login');
        }
    }

}
