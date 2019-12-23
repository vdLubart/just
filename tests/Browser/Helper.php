<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 30.10.19
 * Time: 20:36
 */

namespace Just\Tests\Browser;

use Just\Models\User as UserModel;

class Helper {

    public static function masterUser() {
        return UserModel::where('role', 'master')->first();
    }

}