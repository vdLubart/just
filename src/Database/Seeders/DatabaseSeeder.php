<?php

namespace Just\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('JustStructureSeeder');
        $this->call('JustIconSeeder');
        $this->call('JustDataSeeder');

        $this->command->info('The Just! data are seeded!');
    }
}
