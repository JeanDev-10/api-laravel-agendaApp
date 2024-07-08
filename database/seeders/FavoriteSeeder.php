<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <=3 ; $i++) {
            Favorite::create([
                "user_id"=>1,
                "contact_id"=>$i
            ]);
        }
        for ($i=4; $i <=5 ; $i++) {
            Favorite::create([
                "user_id"=>2,
                "contact_id"=>$i
            ]);
        }

    }
}
