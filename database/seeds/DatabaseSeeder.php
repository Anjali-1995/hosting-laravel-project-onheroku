<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
            // CategoriesTableSeeder::class,
            // AddOnsTableSeeder::class,
            // ProductTableSeeder::class,
            // TagsSeeder::class,
             TagsUserSeeder::class
         ]);
    }
}
