<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('staff')->insert([
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'status' => 'active',
            'phone' => '09-999999999',
            'address' => 'Admin Headquarters',
            'dob' => '1990-01-01', 
            'gender' => 'Other',   
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}