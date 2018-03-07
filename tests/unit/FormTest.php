<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;

use Nickwest\EloquentForms\Test\TestCase;

class FormTest extends TestCase
{
    protected $test_fields;

    public function setUp(){
        parent::setUp();
    }

    public function test_field_can_be_added_to_a_form()
    {
        $field = $this->getFieldData();
        $Form = new Form();

        // Make sure there's no such field already and we get the proper exception
        $this->expectException(InvalidFieldException::class);
        $Form->getField($field['name']);

        // Add the field
        $Form->addField($field['name']);
        $this->assertInstanceOf(Field::class, $Form->getField($field['name']));
    }

    public function test_a_form_can_have_many_fields()
    {
        $field_names = $this->getManyFieldNames(5);

        $Form = new Form();
        $Form->addFields($field_names);

        foreach($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $Form->getField($field_name));
        }
    }

    public function test_a_field_can_be_removed()
    {
        $field = $this->getFieldData();

        $Form = new Form();
        $Form->addField($field['name']);

        $Form->removeField($field['name']);

        $this->expectException(InvalidFieldException::class);
        $Form->getField($field['name']);
    }

    public function test_many_fields_can_be_removed()
    {
        $field_names = $this->getManyFieldNames(5);

        $Form = new Form();
        $Form->addFields($field_names);

        $remove = [$field_names[0], $field_names[2]];
        unset($field_names[0]);
        unset($field_names[2]);
        $Form->removeFields($remove);

        foreach($remove as $field_name) {
            $this->expectException(InvalidFieldException::class);
            $Form->getField($field_name);
        }

        foreach($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $Form->{$field_name});
        }
    }

    public function test_if_a_field_exists()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->assertTrue($Form->isField('first'));
        $this->assertTrue($Form->isField('second'));
        $this->assertFalse($Form->isField('third'));
    }

    public function test_setting_field_value()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $Form->first = 'MyValue1234';
        $Form->second = 'DifferentValue4321';

        $this->assertEquals('MyValue1234', $Form->first->value);
        $this->assertEquals('DifferentValue4321', $Form->second->value);
    }

    public function test_field_value_magic_methods()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();

        // These throw exceptions before the Fields exist
        // __isset
        $this->expectException(InvalidFieldException::class);
        isset($Form->first);

        // __get
        $this->expectException(InvalidFieldException::class);
        $Form->{$field_names[0]};

        // __ set
        $this->expectException(InvalidFieldException::class);
        $Form->first = 'It Works!';


        $Form->addFields($field_names);

        $this->assertFalse(isset($Form->first));

        $Form->first = 'It actually works now';
        $this->assertTrue(isset($Form->first));

        $this->assertEquals($Form->first, 'It actually works now');
    }

    public function test_getting_all_form_values()
    {
        $fields = $this->getManyFieldDatas(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        foreach($fields as $field){
            $Form->{$field['name']} = $field['value'];
        }

        $this->assertEquals(array_column($fields, 'value', 'name'), $Form->getFieldValues());
    }

    public function test_settings_display_fields_on_a_form()
    {
        $fields = $this->getManyFieldDatas(10);

        // Field names only (make sure there aren't duplicates)
        $field_names = array_unique(array_column($fields, 'name'));
        $field_names = array_combine($field_names, $field_names);

        // Create the form
        $Form = new Form();
        $Form->addFields($field_names);


        // Set all fields as display fields
        $Form->setDisplayFields($field_names);

        $this->assertEquals($field_names, $Form->getDisplayFields());

        // remove one field
        $key = array_rand($field_names);
        $removed1 = [$field_names[$key]];
        unset($field_names[$key]);

        $Form->removeDisplayFields($removed1);
        $this->assertEquals($field_names, $Form->getDisplayFields());

        // remove many fields
        $keys = array_rand($field_names, 3);
        $removed = [];
        foreach($keys as $key){
            $removed[] = $field_names[$key];
            unset($field_names[$key]);
        }

        $Form->removeDisplayFields($removed);
        $this->assertEquals($field_names, $Form->getDisplayFields());

        // Add the last fields we removed back in
        $Form->addDisplayFields($removed);
        $field_names = array_merge($field_names, array_combine($removed, $removed));
        $this->assertEquals($field_names, $Form->getDisplayFields());

        // Inject the first field we removed back in after the 3rd
        $key = current(array_slice($field_names, 3, 1));
        $Form->setDisplayAfter(current($removed1), $field_names[$key]);
        $field_names = array_slice($field_names, 0, 3, true) + [current($removed1) => current($removed1)] + array_slice($field_names, 3, null, true);
        $this->assertEquals($field_names, $Form->getDisplayFields());
    }

    public function test_form_fields_can_have_labels_set()
    {
        $fields = $this->getManyFieldDatas(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $labels = array_column($fields, 'label', 'name');

        $Form->setLabels($labels);
        $this->assertEquals($labels, $Form->getLabels());

        // check individual fields too? why not...
        foreach(array_column($fields, 'name') as $field_name) {
            $this->assertEquals($labels[$field_name], $Form->getField($field_name)->label);
        }
    }






    public function test_form_theme_can_be_set_and_it_affects_fields()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        // Verify it starts with the Default theme set
        $this->assertInstanceOf(\Nickwest\EloquentForms\DefaultTheme::class, $Form->getTheme());

        $myTheme = new \Nickwest\EloquentForms\bulma\Theme();
        $Form->setTheme($myTheme);

        $this->assertInstanceOf(\Nickwest\EloquentForms\bulma\Theme::class, $Form->getTheme());
    }

    // public function test_form_can_make_a_view()
    // {
    //     $fields = $this->getManyFieldDatas(5);

    //     $Form = new Form();
    //     $Form->addFields(array_column($fields, 'name'));

    //     $view = $Form->MakeView();
    //     $this->assertInstanceOf(\Illuminate\View\View::class, $view);

    //     // Test that the rendered view is as expected?
    //     $view = $view->render();
    //     var_dump($view);

    // }






///// Test Setup helpers

    private function getManyFieldNames(int $count)
    {
        $field_names = [];
        foreach($this->getManyFieldDatas(5) as $field){
            $field_names[] = $field['name'];
        }

        return $field_names;
    }

    private function getManyFieldDatas(int $count)
    {
        $fields = [];
        for($i = 0; $i < $count; $i++){
            $fields[] = $this->getFieldData();
        }

        return $fields;
    }

    private function getFieldData()
    {
        $Faker = Faker\Factory::create();

        $field = [
            'name' => $Faker->word,
            'length' => $Faker->numberBetween(10, 255),
            'default' => $Faker->name,
            'value' => $Faker->name,
            'type' => 'text',
            'label' => $Faker->state,
        ];

        return $field;
    }

}

?>
