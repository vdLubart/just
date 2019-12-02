<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 30.10.19
 * Time: 20:39
 */

namespace Lubart\Just\Tests\Browser\Master;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Lubart\Just\Tests\Browser\Helper;

class MasterTests extends DuskTestCase {

    protected $user;

    public function setUp():void {
        parent::setUp();

        $this->user = Helper::masterUser();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user);
        });
    }
}
