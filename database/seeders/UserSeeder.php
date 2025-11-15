<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // User::truncate();
        // Schema::enableForeignKeyConstraints();
    
        // User::insert([
        //     'name' => 'Manager',
        //     'email' => 'manager@mail.com',
        //     'password' => Hash::make('123456'),
        //     'role_id'=> 4,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }
}
