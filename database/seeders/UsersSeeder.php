<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() == 'local') {
            $this->call(AdminUserSeeder::class);
        }
    }
}
