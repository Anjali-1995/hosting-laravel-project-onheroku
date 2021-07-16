<?php

use App\Model\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<20;$i++){
            Category::create([
                'name'=>'category_'.($i+1),
                'parent_id'=>0,
                'position'=>0,
            ]);
        }
    }
}
