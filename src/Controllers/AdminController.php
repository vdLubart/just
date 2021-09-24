<?php

namespace Just\Controllers;

use Illuminate\Support\Facades\Config;

class AdminController extends Controller
{
    public function __construct() {
        parent::__construct();

        Config::set('isAdmin', true);
    }
}
