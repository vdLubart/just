<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 24.10.19
 * Time: 8:32
 */

namespace Just\Tests\Feature\Settings;

use Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions {

    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test */
    function master_can_access_settings_home_page() {
        $this->access_settings_home_page(true);
    }

}
