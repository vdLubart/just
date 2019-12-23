<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 24.10.19
 * Time: 8:32
 */

namespace Just\Tests\Feature\Languages;

class GuestAccessTest extends Actions {

    /** @test */
    public function guest_can_access_language_list() {
        $this->access_language_list(false);
    }

}
