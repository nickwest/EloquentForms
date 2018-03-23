<?php namespace Nickwest\EloquentForms\Test;

use Cache;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Nickwest\EloquentForms\FormTrait;
use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // SQLite
        $app['config']->set('database.default', env('DB_CONNECTION'));
    }

    public function setUp() {
        parent::setUp();

        // Clear any cache
        Cache::flush();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Nickwest\EloquentForms\EloquentFormsServiceProvider::class,
            \Nickwest\EloquentForms\Themes\bulma\EloquentFormsBulmaThemeServiceProvider::class,
        ];
    }


    ///// Test Setup helpers

    protected function createComplexForm()
    {

        $this->fields = $this->getFieldData(15);

        $Form = new Form();
        $Form->addFields(array_column($this->fields, 'name'));

        $Form->{$this->fields[3]['name']}->options->setOptions(['Yes', 'No']);

        // Set some validation rules
        $this->validation_rules = [
            $this->fields[0]['name'] => 'required|date|after:tomorrow',
            $this->fields[1]['name'] => 'email',
            $this->fields[2]['name'] => 'required|email',
            $this->fields[3]['name'] => [
                'required',
                Rule::in($Form->{$this->fields[3]['name']}->options->getOptions()),
            ],
        ];
        $Form->setValidationRules($this->validation_rules);

        $this->fields[0]['value'] = date('Y-m-d', strtotime('now +14 days'));
        $this->fields[1]['value'] = ''; // Not set, should still pass
        $this->fields[2]['value'] = $this->Faker->email;
        $this->fields[3]['value'] = 'Yes';

        // Set all field values
        foreach($this->fields as $field) {
            $Form->{$field['name']}->attributes->value = $field['value'];
        }

        // Add a subform
        $this->sub_fields = $this->getFieldData(3);
        $subForm = new Form();
        $subForm->addFields(array_column($this->sub_fields, 'name'));

        $Form->addSubform('test_subform', $subForm);

        // Set all Fields as display fields
        $field_names = array_column($this->fields, 'name');
        $field_names = array_combine($field_names, $field_names);
        $Form->setDisplayFields($field_names);


        return $Form;
    }

    protected function getManyFieldNames(int $count)
    {
        $field_names = [];
        foreach($this->getFieldData(5) as $field){
            $field_names[] = $field['name'];
        }

        return $field_names;
    }

    protected function getFieldData(int $count = 1)
    {
        $fields = [];
        for($i = 0; $i < $count; $i++){
            $fields[] = [
                'name' => $this->Faker->unique()->word,
                'length' => $this->Faker->numberBetween(10, 255),
                'default_value' => $this->Faker->name,
                'value' => $this->Faker->name,
                'type' => 'text',
                'label' => $this->Faker->state,
            ];
        }

        if($count == 1){
            return current($fields);
        }

        return $fields;
    }

    protected function getComplexField()
    {
        $Field = new Field('my_field');

        $Field->CustomField = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField;

        $test_options = ['1' => 'one', '2' => 'two', '44' => 'Fourtyfour'];
        $Field->options->setOptions($test_options);

        $Field->options->setDisabledOptions(['1','44']);

        $Field->label_suffix = ':';
        $Field->example = 'This is an example';
        $Field->note = 'This is a note';
        $Field->link = 'https://google.com';
        $Field->error_message = 'Oh no it\'s an error';
        $Field->example = 'This is an example';
        $Field->default_value = 44;
        $Field->is_inline = true;
        $Field->validation_rules = 'required|integer';
        $Field->label_class = 'label_class_goes_here';
        $Field->container_class = 'yay';
        $Field->options->container_class = 'options';
        $Field->input_wrapper_class = 'Snoop-Dogg';
        $Field->options->wrapper_class = 'Macklemore';
        $Field->options->label_class = 'so_many_classes';

        return $Field;
    }

    protected function getSubformField()
    {
        $Field = new Field('my_field');

        $Form = new Form();
        $Form->addFields(['sub1', 'sub2']);

        $Field->Subform = $Form;

        return $Field;
    }

    protected function getSimulatedPostValues()
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

    protected function expectedDBStructure($is_mySql = false)
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

    protected function columnArray($name, $type='string', $default=null, $length=null, $values=null)
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


/**
 * Sample model for testing with a real model
 */
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

        $this->Form()->is_hidden->options->setOptions([
            1 => 'Yes',
        ]);

        $this->Form()->fruits_liked->options->setOptions([
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

        $this->Form()->actors_liked->options->setOptions([
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

        $this->Form()->good_day->options->setOptions(['Yes' => 'Yes', 'No' => 'No']);

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
                Rule::in($this->Form()->good_day->options->getOptions()),
            ],
            'beverage' => [
                Rule::in($this->Form()->beverage->options->getOptions()),
            ],
            'volume' => 'numeric',
        ]);
    }


}
