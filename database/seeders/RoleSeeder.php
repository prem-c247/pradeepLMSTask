<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesArray  = [
            [
                'name' => 'Admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'School',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teacher',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Student',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Role::insert($rolesArray);
    }
}
