<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TeamsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(MapsTableSeeder::class);

        echo 'Also make sure to run : php artisan permissions:import'. PHP_EOL;
    }
}
