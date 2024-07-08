<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'firstname' => 'test',
            'lastname' => 'uno',
            'email' => 'test@test.com',
            'password' => Hash::make('123456')
        ]);
        DB::table('users')->insert([
            'firstname' => 'jean',
            'lastname' => 'rodriguez',
            'email' => 'jean@hotmail.com',
            'password' => Hash::make('jean1234')
        ]);
    }
}
