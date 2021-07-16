<?php

use App\Ingredients;
use App\Model\Product;
use App\Nutrients;
use App\Recipe;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        
        

        for($i=0;$i<4000;$i++){

            $add_ons = [];

            for($j=0;$j<5;$j++){
                $x = $faker->numberBetween(10,80);
                while(array_search($x,$add_ons)){
                    $x = $faker->numberBetween(10,80);
                }
                array_push($add_ons,$x);
            }

            $p_id=Product::insertGetId([
                'name' => $faker->unique()->name(),
                'description' => $faker->text(500),
                'image' => 'def.png',
                'price' => $faker->numberBetween(60,500),
                'category_ids' => json_encode([
                    [
                        "id"=>$faker->numberBetween(2,8),
                        "position"=>1
                    ]
                ]),
                'variations' => json_encode([]),
                'add_ons' =>   json_encode($add_ons),
                
                'available_time_starts'=>'12:00',
                'available_time_ends'=>'24:00',
            ]);
    
            for($j=0;$j<5;$j++){
                Ingredients::create([
                    'product_id'=>$p_id,
                    'quantity'=>$faker->numberBetween(1,5),
                    'name'=>$faker->name(),
                    'price'=>$faker->randomFloat(4,50,),
                    'minimum_integer'=>$faker->numberBetween(2,5)
                ]);
            }
    
            Recipe::create([
                'product_id'=>$p_id,
                'description'=>$faker->text(),
                'procedure'=>json_encode(
                   [
                    $faker->unique()->text(),
                    $faker->unique()->text(),
                    $faker->unique()->text(),
                    $faker->unique()->text()
                   ]
                )
            ]);
    
            Nutrients::create([
                "product_id"=>$p_id,
                "glycemic_index"=>$faker->randomFloat(3,0.2),
                "glycemic_load"=>$faker->randomFloat(3,0.2),
                "score"=>$faker->randomFloat(3,0.2),
                "protein"=>$faker->randomFloat(3,0.2),
                "calories"=>$faker->randomFloat(3,0.2),
                "carbs"=>$faker->randomFloat(3,0.2),
                "energy"=>$faker->randomFloat(3,0.2),
                "fats"=>$faker->randomFloat(3,0.2),
                "fiber"=>$faker->randomFloat(3,0.2),
                "sugar"=>$faker->randomFloat(3,0.2),
                "suggestion"=>$faker->text()
            ]);
    
        }


    }
}