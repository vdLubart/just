<?php

namespace Just\Database\Seeders;

use Illuminate\Database\Seeder;
use Just\Models\System\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Just\Models\System\IconSet;

class JustUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
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
                    'table' => ''
                ]);

                DB::table('blockList')->insert([
                    'block' => 'html',
                    'table' => 'texts'
                ]);

            case version_compare(Version::current(), '1.2.0', '<'):
                // some code applied starting from v.1.2.0
                IconSet::where('title', 'Font Awesome')->delete();

                Artisan::call("db:seed", ["--class" => "Just\\Database\\Seeds\\JustIconSeeder"]);

                Schema::table('themes', function(Blueprint $table){
                    $table->increments('id');
                });
            case version_compare(Version::current(), '1.3.0', '<'):
                DB::table('blockList')->insert([
                    'block' => 'events',
                    'table' => 'events'
                ]);
        }
    }
}
