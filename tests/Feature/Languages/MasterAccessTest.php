<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 24.10.19
 * Time: 8:32
 */

namespace Lubart\Just\Tests\Feature\Languages;

use Lubart\Just\Tests\Feature\Helper;

class MasterAccessTest extends Actions {

    use Helper;

    protected function setUp(): void {
        parent::setUp();

        $this->actingAsMaster();
    }

    /** @test */
    public function master_can_access_language_list() {
        $this->access_language_list(true);
    }

}
