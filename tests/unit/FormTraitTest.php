<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


use Nickwest\EloquentForms\FormTrait;
use Nickwest\EloquentForms\Test\TestCase;


class FormTraitTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->Faker = Faker\Factory::create();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations/'));

        // Sample is a class declared in the bottom of this file
        // It is only used in these tests
        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    // This test runs with SQLite as the Driver.
    // It uses default Doctrine/Dbal to get the column info to generate the form fields
    public function test_formtrait_will_generate_a_form_from_a_db_structure()
    {
        $array = $this->Model->getColumnsArray();

        $this->assertEquals($this->expectedDBStructure(), $array);
    }

    // This test runs with MySQL as the Driver
    // It uses Raw MySQL queries to get extra column info and set more form field data on generation
    public function test_form_trait_will_generate_a_form_using_extra_mysql_field_data_db_structure()
    {
        // If MySQL connection fails, then skip this test
        try{
            // Switch to MySQL
            \Config::set('database.default', 'mysql');

            // Rerun setup
            $this->setUp();
        } catch(\Exception $e){
            $this->markTestSkipped(
                'The MySQL Connection is not working. See phpunit.xml to add connection info'
              );
        }
        $array = $this->Model->getColumnsArray();

        $this->assertEquals($this->expectedDBStructure(true), $array);
    }

    public function test_formtrait_form_returns_a_form_object()
    {
        $this->assertInstanceOf(\Nickwest\EloquentForms\Form::class, $this->Model->Form());
    }

    public function test_formtrait_getFormView_returns_a_view()
    {
        $view = $this->Model->getFormView([]);

        $this->assertInstanceOf(View::class, $view);
    }

    public function test_formtrait_getFieldView_returns_a_view()
    {
        foreach($this->Model->Form()->getFields() as $Field){
            $this->assertInstanceOf(View::class, $this->Model->getFieldView($Field->attributes->name));
        }
    }

    public function test_formtrait_getFieldDisplayView_returns_a_view()
    {
        foreach($this->Model->Form()->getFields() as $Field){
            $this->assertInstanceOf(View::class, $this->Model->getFieldDisplayView($Field->attributes->name));
        }
    }

    public function test_formtrait_setAllFormValues_sets_values_to_the_form_from_the_model()
    {
        $post_data = $this->getSimulatedPostValues();
        foreach($post_data as $field => $value){
            if(is_array($value)){
                $value = implode('|', $value);
            }
            $this->Model->{$field} = $value;
        }

        // Set the form values from the Model's values
        $this->Model->setAllFormValues();

        // getFieldValues is tested in FormTests
        $values = $this->Model->Form()->getFieldValues();


        // Unset ID and timestamps since they're not in the post data used to generate the model
        unset($values['id']);
        unset($values['created_at']);
        unset($values['updated_at']);
        unset($values['volumes']); // Datalists arenn't real fields either

        // The form should now have equal values to what's in post_data,
        $this->assertEquals($post_data, $values);
    }

    public function test_formtrait_setPostValues_sets_post_data_to_the_form_and_the_model()
    {
        $post_data = $this->getSimulatedPostValues();
        // Id is not fillable
        $post_data['id'] = 100;

        // Try to set $post_data with ID
        $this->Model->setPostValues($post_data);

        // remove id from post_data for comparison
        $post_data['id'];

        // If ID got set, these be equal and fail
        $this->assertNotEquals(100, $this->Model->id);
        $this->assertNotEquals(100, $this->Model->Form()->id->attributes->value);
    }

    public function test_formtrait_setPostValues_obeys_model_fillable_settings()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        foreach($post_data as $field => $value) {
            $this->assertEquals($value, $this->Model->Form()->{$field}->attributes->value);
            // on the model arrays are flattened
            if(is_array($value)){
                $value = implode('|', $value);
            }
            $this->assertEquals($value, $this->Model->{$field});
        }
    }

    public function test_fortrait_setPostValues_can_handle_multi_key_fields()
    {
        $post_data = $this->getSimulatedPostValues();
        $post_data['fruits_liked'] = ['Banana', 'Apple', 'Peach'];

        $this->Model->setPostValues($post_data);

        // The model should have flattened values
        $expected = 'Banana|Apple|Peach';

        $this->assertEquals($post_data['fruits_liked'], $this->Model->Form()->fruits_liked->attributes->value);
        $this->assertEquals($expected, $this->Model->fruits_liked);
    }

    public function test_formtrait_models_force_validation_on_save_by_default()
    {
        $this->assertAttributeEquals(true, 'validate_on_save', $this->Model);
    }

    public function test_formtrait_adds_extra_validation_and_that_works()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($this->getSimulatedPostValues());

        // foreach($this->Model->Form()->getFields() as $Field){
        //     if($Field->error_message != ''){
        //         dd($Field->attributes->name.' '.$Field->error_message);
        //     }
        // }

        // Simple case, completely valid
        $this->assertTrue($this->Model->isFormValid());
    }

    public function test_formtrait_models_validate_on_save_will_run_validation_before_saving()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        // Make it invalid
        $this->Model->email = 'NotEmail';

        // Make sure it's not valid
        $this->assertFalse($this->Model->isFormValid());

        // Trying to save should fail and return false
        $this->assertFalse($this->Model->save());
    }

    public function test_formtrait_models_can_block_validation_on_save()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        // Make it invalid
        $this->Model->email = 'not_an_email1234';

        $this->Model->validate_on_save = false;

        $this->assertTrue($this->Model->save());
    }

    public function test_formtrait_save_works_when_form_not_prepared()
    {
        $Sample = new Sample;

        // Set the two not-nullable fields
        $Sample->first_name = 'Tester';
        $Sample->last_name = 'McTesting';

        // Save without preparing the form
        $this->assertTrue($Sample->save());
    }

    public function test_formtrait_save_works_when_form_not_prepared_but_form_validation_doesnt_work()
    {
        $Sample = new Sample;

        // Set the two not-nullable fields
        $Sample->first_name = 'Tester';
        $Sample->last_name = 'McTesting';
        $Sample->email = 'not_an_email_address';

        // Save without preparing the form
        $this->assertTrue($Sample->save());
    }

    public function test_formtrait_generateFormFromJson_works()
    {
        // This is testing in FormTests
        $json = $this->Model->Form()->toJson();

        $newModel = new Sample();
        $newModel->generateFormFromJson($json);

        // Their forms should be equal
        $this->assertEquals($this->Model->Form(), $newModel->Form());
    }


    ////// Helpers

    private function getSimulatedPostValues()
    {
        $this->Faker->addProvider(new \Faker\Provider\en_US\PhoneNumber($this->Faker));

        return [
            'first_name' => $this->Faker->name,
            'last_name' => $this->Faker->name,
            'email' => $this->Faker->email,
            'password' => $this->Faker->sha1,
            'file_name' => '/'.$this->Faker->word.'/'.$this->Faker->word.'/'.$this->Faker->word.'.'.$this->Faker->fileExtension,
            'favorite_number' => $this->Faker->numberBetween(10, 5000),
            'is_hidden' => [$this->Faker->numberBetween(0,1)],
            'favorite_season' => $this->Faker->randomElement(['', 'Winter', 'Spring', 'Summer', 'Autumn']),
            'beverage' => '',
            'fruits_liked' => ['Banana', 'Peach'],
            'actors_liked' => ['RDN', 'MF'],
            'favorite_color' => $this->Faker->hexcolor,
            'good_day' => $this->Faker->randomElement(['', 'Yes', 'No']),
            'favorite_date' => $this->Faker->date,
            'favorite_days' => ['T'],
            'birthday' => $this->Faker->date,
            'volume' => $this->Faker->numberBetween(1, 10),
            'favorite_month' => $this->Faker->month,
            'phone_number' => $this->Faker->phoneNumber,
            'time' => $this->Faker->time,
            'website_url' => $this->Faker->word,
            'week_year' => $this->Faker->word,
            'story' => $this->Faker->text,
        ];


    }

    private function expectedDBStructure($is_mySql = false)
    {
        if($is_mySql){
            return [
                'id' => $this->columnArray('id', 'int', null, 10),
                'created_at' => $this->columnArray('created_at', 'timestamp'),
                'updated_at' => $this->columnArray('updated_at', 'timestamp'),
                'first_name' => $this->columnArray('first_name', 'varchar', null, 255),
                'last_name' => $this->columnArray('last_name', 'varchar', null, 255),
                'email' => $this->columnArray('email', 'varchar', null, 255),
                'password' => $this->columnArray('password', 'varchar', null, 255),
                'file_name' => $this->columnArray('file_name', 'varchar', null, 255),
                'favorite_number' => $this->columnArray('favorite_number', 'int', null, '11'),
                'is_hidden' => $this->columnArray('is_hidden', 'tinyint', null, 1),
                'favorite_season' => $this->columnArray('favorite_season', 'enum', null, null, ['' => '-- Select One --', 'Winter' => 'Winter', 'Spring' => 'Spring','Summer' => 'Summer','Autumn' => 'Autumn']),
                'beverage' => $this->columnArray('beverage', 'enum', null, null, ['' => '-- Select One --', 'Beer' => 'Beer', 'Wine' => 'Wine', 'Water' => 'Water']),
                'fruits_liked' => $this->columnArray('fruits_liked', 'varchar', null, 255),
                'actors_liked' => $this->columnArray('actors_liked', 'varchar', null, 255),
                'favorite_color' => $this->columnArray('favorite_color', 'varchar', null, 255),
                'good_day' => $this->columnArray('good_day', 'enum', null, null, ['' => '-- Select One --', 'Yes' => 'Yes', 'No' => 'No']),
                'favorite_date' => $this->columnArray('favorite_date', 'date'),
                'favorite_days' => $this->columnArray('favorite_days', 'varchar', null, 255),
                'birthday' => $this->columnArray('birthday', 'date'),
                'volume' => $this->columnArray('volume', 'int', null, 11),
                'favorite_month' => $this->columnArray('favorite_month', 'varchar', null, 255),
                'phone_number' => $this->columnArray('phone_number', 'varchar', null, 255),
                'time' => $this->columnArray('time', 'varchar', null, 255),
                'website_url' => $this->columnArray('website_url', 'varchar', null, 255),
                'week_year' => $this->columnArray('week_year', 'varchar', 40, 255),
                'story' => $this->columnArray('story', 'text', null, 65535),
            ];
        }

        return [
            'id' => $this->columnArray('id', 'integer'),
            'created_at' => $this->columnArray('created_at', 'datetime'),
            'updated_at' => $this->columnArray('updated_at', 'datetime'),
            'first_name' => $this->columnArray('first_name'),
            'last_name' => $this->columnArray('last_name'),
            'email' => $this->columnArray('email'),
            'password' => $this->columnArray('password'),
            'file_name' => $this->columnArray('file_name'),
            'favorite_number' => $this->columnArray('favorite_number', 'integer'),
            'is_hidden' => $this->columnArray('is_hidden', 'boolean', null, 1),
            'favorite_season' => $this->columnArray('favorite_season'),
            'beverage' => $this->columnArray('beverage'),
            'fruits_liked' => $this->columnArray('fruits_liked'),
            'actors_liked' => $this->columnArray('actors_liked'),
            'favorite_color' => $this->columnArray('favorite_color'),
            'good_day' => $this->columnArray('good_day'),
            'favorite_date' => $this->columnArray('favorite_date', 'date'),
            'favorite_days' => $this->columnArray('favorite_days'),
            'birthday' => $this->columnArray('birthday', 'datetime'),
            'volume' => $this->columnArray('volume', 'integer'),
            'favorite_month' => $this->columnArray('favorite_month'),
            'phone_number' => $this->columnArray('phone_number'),
            'time' => $this->columnArray('time'),
            'website_url' => $this->columnArray('website_url'),
            'week_year' => $this->columnArray('week_year', 'string', 40),
            'story' => $this->columnArray('story', 'text'),
        ];
    }

    private function columnArray($name, $type='string', $default=null, $length=null, $values=null)
    {
        return [
            'name' => $name,
            'type' => $type,
            'default' => $default,
            'length' => $length,
            'values' => $values
        ];
    }
}


class Sample extends Model
{
    use FormTrait;

    public $table = 'sample';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'file_name', 'favorite_number', 'favorite_season', 'fruits_liked',
        'favorite_color', 'good_day', 'favorite_date', 'favorite_days', 'birthday', 'age', 'favorite_month', 'phone_number',
        'time', 'website_url', 'week_year', 'beverage', 'story', 'actors_liked', 'volume',
        'is_hidden'
    ];

    public function prepareForm()
    {
        // Default Form Field label postfix
        $this->setLabelSuffix(':');

        // This is magical and comes from the FormTrait. It generates form field data by looking at the model's table columns
        $this->generateFormData();

        $this->Form()->getSubmitButton('submit_button', 'Submit')->attributes->value = 'Save';
        $this->Form()->getSubmitButton('submit_button', 'Submit')->label = 'Save';

        $this->Form()->setTheme(new \Nickwest\EloquentForms\Themes\bulma\Theme());

        // Set field types for fields that aren't just default types
        $this->Form()->setTypes([
            'beverage' => 'radio',
            'birthday' => 'datetime-local',
            'fruits_liked' => 'checkbox',
            'email' => 'email',
            'favorite_date' => 'date',
            'favorite_days' => new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField(),
            'favorite_color' => 'color',
            'file_name' => 'file',
            'favorite_month' => 'month',
            'favorite_number' => 'number',
            'password' => 'password',
            'phone_number' => 'tel',
            'time' => 'time',
            'week_year' => 'week',
            'website_url' => 'url',
            'volume' => 'range',
            'actors_liked' => 'select',
            'is_hidden' => 'checkbox'
        ]);

        // By Default all fields will be displayed

        // We can set specific fields to be displayed by the form
        // $this->Form()->setDisplayFields( array(
        //     'first_name', 'last_name',
        //     'email', 'password',
        //     'phone_number',
        //     'time',
        //     'website_url',
        //     'week_year',
        //     'beverage',
        // ));

        // Or we can remove only selected display fields.
        $this->Form()->removeDisplayFields([
            'id',
            'created_at',
            'updated_at',
        ]);

        // Override delete button value
        $this->Form()->file_name->file_delete_button_value = 'Remove';

        // Set custom labels for some of our fields
        $this->Form()->setLabels([
            'fruits_liked' => 'Which fruits do you like?',
            'good_day' => 'Are you having a good day?',
            'time' => 'Current time',
            'website_url' => 'Link to your website',
            'actors_liked' => 'Which of these actors do you like?'
        ]);

        // Set which fields are required (label view purposes only right now)
        $this->Form()->setRequiredFields([
            'first_name', 'last_name'
        ]);

        // Setting inline fields
        // When a field is inline then it will be on the same line as the next field
        // If you have 3 inline fields in a row, all 4 fields will be on the same line
        $this->Form()->setInline(['first_name']);

        $this->Form()->is_hidden->setOptions([
            1 => 'Yes',
        ]);

        $this->Form()->fruits_liked->setOptions([
            'banana' => 'Banana',
            'strawberry' => 'Strawberry',
            'apple' => 'Apple',
            'mango' => 'Mango',
            'passion Fruit' => 'Passion Fruit',
            'orange' => 'Orange',
            'kiwi' => 'Kiwi',
            'pear' => 'Pear',
            'pineapple' => 'Pineapple'
        ]);

        $this->Form()->actors_liked->setOptions([
            'RDN' => 'Robert De Niro',
            'MF' => 'Morgran Freeman',
            'CE' => 'Clint Eastwood',
            'AP' => 'Al Pacino',
            'TH' => 'Tom Hanks',
            'MD' => 'Matt Damon',
        ]);
        $this->Form()->actors_liked->attributes->multi_key = true;

        // Turn off the auto injection of CSRF Token Field
        //$this->Form()->laravel_csrf = false;

        $this->Form()->good_day->setOptions(['Yes' => 'Yes', 'No' => 'No']);

        $this->Form()->setExamples([
            'phone_number' => 'ex: 206-685-9937',
        ]);

        $this->Form()->volume->attributes->min = 5;
        $this->Form()->volume->attributes->max = 40;
        $this->Form()->volume->attributes->step = 5;
        $this->Form()->volume->attributes->list = 'volumes';

        $this->Form()->addDatalist('volumes', [ 5 => '5', 10 => '', 15 => '', 20 => '20', 25 => '', 30 => '', 35 => '', 40 => '40']);

        $this->Form()->phone_number->attributes->pattern = '\\d{3}[\\-]\\d{3}[\\-]\\d{4}';
        $this->Form()->phone_number->attributes->placeholder = '123-456-7890';

        // $this->Form()->allow_delete = true;

        // Set a custom field ID
        //$this->Form()->phone->id = 'override-id';

        $this->Form()->setValidationRules([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email',
            'good_day' => [
                Rule::in($this->Form()->good_day->getOptions()),
            ],
            'beverage' => [
                Rule::in($this->Form()->beverage->getOptions()),
            ],
            'volume' => 'numeric',
        ]);
    }


}
