<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <=25 ; $i++) {
            Favorite::create([
                "user_id"=>1,
                "contact_id"=>$i
            ]);
        }
        for ($i=26; $i <=50 ; $i++) {
            Favorite::create([
                "user_id"=>2,
                "contact_id"=>$i
            ]);
        }

    }
}
