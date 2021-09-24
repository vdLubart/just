<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 24.10.19
 * Time: 8:32
 */

namespace Just\Tests\Feature\Settings;

class GuestAccessTest extends Actions {

    /** @test */
    function guest_cannot_access_settings_home_page() {
        $this->access_settings_home_page(false);
    }

}
