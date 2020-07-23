<?php

use Illuminate\Database\Seeder;

class MapsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Map::class, 1000)->create()->each(function ($map) {
            if (RAND(0,100) > 30) {
                for ($i=0; $i < rand(10,100); $i++) { 
                    $map->questions()->save(factory(App\Question::class)->make());
                }
            }
        });
        
        foreach (\App\Map::all()->random(7)->push(\App\Map::first()) as $map) {
            $map->update(['published' => true]);
        }
    }
}
