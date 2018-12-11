<?php

namespace Lubart\Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Lubart\Just\Models\Version;

class JustUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Version::inComposer() == "9999999-dev"){
            return;
        }
        
        switch (true){
            case version_compare(Version::current(), '1.0.1', '<'):
                // some code applied starting from v.1.0.1
                // dont use break; here
            case version_compare(Version::current(), '1.0.2', '<'):
                // some code applied starting from v.1.0.2
        }
    }
}
