<?php

namespace Database\Seeders;

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
         \App\Models\Adverts::factory(7)->create();
         
         // Create default admin user
         \App\Models\Admin::create([
             'firstName' => 'Admin',
             'lastName' => 'User',
             'email' => 'ore@gmail.com',
             'password' => bcrypt('ore123-'),
             'type' => 'Super Admin',
             'status' => 'active'
         ]);
    }
}
