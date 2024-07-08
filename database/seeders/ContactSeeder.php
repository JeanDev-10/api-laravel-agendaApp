<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contacts')->insert([
            [
                'name' => 'Juanito',
                'phone' => '0993954831',
                'user_id' => 1
            ],
            [
                'name' => 'Miguelito',
                'phone' => '0985789475',
                'user_id' => 1
            ],
            [
                'name' => 'Celorio',
                'phone' => '0985789423',
                'user_id' => 1
            ],
            [
                'name' => 'Yimmito',
                'phone' => '0985789422',
                'user_id' => 2
            ],
            [
                'name' => 'Pipo',
                'phone' => '0985789425',
                'user_id' => 2
            ],
        ]);
    }
}
