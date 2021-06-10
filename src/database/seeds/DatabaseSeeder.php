<?php

namespace Just\Database\Seeds;

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

        $this->command->info('Data Just! seeded!');
    }
}
