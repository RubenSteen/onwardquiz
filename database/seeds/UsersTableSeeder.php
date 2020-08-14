<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seedAmount = 1000;

        $teams = \App\Team::all();

        factory(App\User::class, $seedAmount)->create()->each(function ($user) use ($teams) {

            // Make user 1 and 3 super admin
            if ($user->id === 1 || $user->id === 3) {
                $user->update(['super_admin' => true]);
            }

            // confirm a few users
            if ($user->id === 1 || $user->id === 2 || $user->id === 4 || $user->id === 9) {
                $user->update(['confirmed' => true]);
                if ($user->id === 2) {
                    $user->update(['editor' => true]);
                }
            }

            // Make user 2 and 5 banned
            if ($user->id === 2 || $user->id === 5) {
                $user->update(['banned' => true]);
            }

            if (rand(0, 100) > 30) {
                $user->teams()->attach($teams->random(rand(1, 50))->pluck('id'));
            }
        });
    }
}
