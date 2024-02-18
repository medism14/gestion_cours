<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '0033788888888',
            'role' => 0,
            'sexe' => 'H',
            'first_connection' => true,
            'password' => bcrypt('admin'),
        ]);
    }
}
