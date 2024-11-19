<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['id' => 1], [
            'role_id' => 1, // Admin role Id
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 123456,
            'status' => 'Active'
        ]);
    }
}
