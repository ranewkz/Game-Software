<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Re-populating the role table so your users have a valid ID to reference
        DB::table('role')->insert([
            ['id' => 1, 'name' => 'Staff'],
            ['id' => 2, 'name' => 'Customer'],
        ]);
    }
}