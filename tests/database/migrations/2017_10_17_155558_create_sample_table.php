<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sample');
        Schema::create('sample', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('favorite_number')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->enum('favorite_season', array('', 'Winter', 'Spring', 'Summer', 'Autumn'))->nullable();
            $table->enum('beverage', array('', 'Beer', 'Wine', 'Water'))->nullable();
            $table->string('fruits_liked')->nullable();
            $table->string('actors_liked')->nullable();
            $table->string('favorite_color')->nullable();
            $table->enum('good_day', array('', 'Yes', 'No'))->nullable();
            $table->date('favorite_date')->nullable();
            $table->string('favorite_days')->nullable();
            $table->datetime('birthday')->nullable();
            $table->integer('volume')->nullable();
            $table->string('favorite_month')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('time')->nullable();
            $table->string('website_url')->nullable();
            $table->string('week_year')->default('40')->nullable();
            $table->text('story')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample');
    }
}
