<?php

use App\Tags;
use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       

        for($i=1;$i<10000;$i++){
            Tags::create([
                'key'=>'tag '.($i+1)
            ]);
        }
    }
}
