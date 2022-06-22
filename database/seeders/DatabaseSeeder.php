<?php

namespace Database\Seeders;

use App\Models\InteractionStatus;
use App\Models\User;
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
        $account_seeder = new AccountSeeder();
        $account_types = $account_seeder->run();
        $user_seeder = new UserSeeder($account_types);
        $user_seeder->run();
        $status_seeder = new InteractionStatusSeeder();
        $status_seeder->run();
    }
}
