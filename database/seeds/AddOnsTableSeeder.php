<?php

use App\Model\AddOn;
use Faker\Factory;
use Illuminate\Database\Seeder;

class AddOnsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        for($i=0;$i<500;$i++){
            AddOn::create([
                'name'=>$faker->unique()->name(),
                'price'=>$faker->numberBetween(0,100)
            ]);
        }
    }
}
