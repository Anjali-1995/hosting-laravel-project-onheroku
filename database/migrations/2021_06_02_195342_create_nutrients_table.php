<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutrientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrients', function (Blueprint $table) {
            $table->id();
            

            $table->unsignedBigInteger('product_id');
            $table->integer('glycemic_index');
            $table->integer('glycemic_load');
            $table->double("score");
            $table->double("protein");
            $table->double("calories");
            $table->double("carbs");
            $table->double("energy");
            $table->double("fats");
            $table->double("fiber");
            $table->double("sugar");
            $table->string("suggestion");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nutrients');
    }
}
