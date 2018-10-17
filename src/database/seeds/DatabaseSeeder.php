<?php

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
        $this->call('JustDataSeeder');
        
        $this->command->info('Data Just! seeded!');
    }
}
