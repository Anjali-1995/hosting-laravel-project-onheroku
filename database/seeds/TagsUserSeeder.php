<?php

use App\TagsUser;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TagsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        for($i=8124;$i<=12123;$i++){
            for($j=0;$j<5;$j++){
                TagsUser::create([
                    'product_id'=>$i,
                    'tag_id'=>$faker->numberBetween(1,10000)
                ]);
            }
        }
    }
}
