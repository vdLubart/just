<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 24.10.19
 * Time: 8:32
 */

namespace Just\Tests\Feature\Settings;

use Just\Tests\Feature\Helper;

class AdminAccessTest extends Actions {

    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /** @test */
    function admin_can_access_settings_home_page() {
        $this->access_settings_home_page(true);
    }

}
