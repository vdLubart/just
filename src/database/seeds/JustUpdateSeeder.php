<?php

namespace Lubart\Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Lubart\Just\Models\Version;
use Illuminate\Support\Facades\DB;
use Lubart\Just\Models;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
                
            case version_compare(Version::current(), '1.2.0', '<'):
                // some code applied starting from v.1.2.0
                Models\IconSet::where('title', 'Font Awesome')->delete();
                
                Artisan::call("db:seed", ["--class" => "Lubart\\Just\\Database\\Seeds\\JustIconSeeder"]);
                
                Schema::table('themes', function(Blueprint $table){
                    $table->increments('id');
                });
        }
    }
}
