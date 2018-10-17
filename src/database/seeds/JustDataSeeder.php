<?php

namespace Lubart\Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Lubart\Just\Models;

class JustDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Users
        Models\User::insert([
			[
		        'role' => 'admin',
		        'name' => 'Admin',
		        'email' => 'admin@just-use.it',
		        'password' => bcrypt('admin')
		    ],
		    [
		        'role' => 'master',
		        'name' => 'Master',
		        'email' => 'master@just-use.it',
		        'password' => bcrypt('master')
		    ]
		]);
    }
}
