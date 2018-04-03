<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;
use Config;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Nickwest\EloquentForms\Test\Sample;
use Nickwest\EloquentForms\Test\TestCase;

class DataFromMySQLTraitTest extends TestCase
{
    // use RefreshDatabase; // A bug in Laravel <5.6.4 causes this to fail tests

    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations/'));

        // Sample is a class declared in the bottom of this file
        // It is only used in these tests
        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    // This test runs with MySQL as the Driver
    // It uses Raw MySQL queries to get extra column info and set more form field data on generation
    public function test_form_trait_will_generate_a_form_using_extra_mysql_field_data_db_structure()
    {
        // If MySQL connection fails, then skip this test
        try{
            // Switch to MySQL
            Config::set('database.default', 'mysql');

            // Rerun setup
            $this->setUp();
        } catch(\Exception $e){
            throw $e;
            $this->markTestSkipped(
                'The MySQL Connection is not working. See phpunit.xml to add connection info'
              );
        }
        $array = $this->Model->getColumnsArray();

        $this->assertEquals($this->expectedDBStructure(true), $array);
    }


}
