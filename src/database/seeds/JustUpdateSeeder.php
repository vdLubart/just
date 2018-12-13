<?php

namespace Lubart\Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Lubart\Just\Models\Version;
use Illuminate\Support\Facades\DB;

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
            // Version::current() - version stored in DB. It is version before update
            // '1.1.0' - version where changed should be seeded
            case version_compare(Version::current(), '1.1.0', '<'):
                // some code applied starting from v.1.1.0
                // dont use break; here
                DB::table('blockList')->insert([
                    'block' => 'space',
                    'title' => "Empty",
                    'description' => "Adds empty space with fixed height",
                    'table' => ''
                ]);
                
                DB::table('blockList')->insert([
                    'block' => 'html',
                    'title' => "HTML",
                    'description' => "Adds HTML piece of code",
                    'table' => 'texts'
                ]);
                
            case version_compare(Version::current(), '1.1.1', '<'):
                // some code applied starting from v.1.0.2
        }
    }
}
