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


    public function test_form_addField_adds_a_field_to_the_form()
    {
        $field = $this->getFieldData();
        $Form = new Form();

        // Add the field
        $Form->addField($field['name']);
        $this->assertInstanceOf(Field::class, $Form->getField($field['name']));
    }

    public function test_form_getField_throws_exception_on_invalid_field()
    {
        $field = $this->getFieldData();
        $Form = new Form();

        // Make sure there's no such field already and we get the proper exception
        $this->expectException(InvalidFieldException::class);
        $Form->getField($field['name']);
    }

    public function test_form_addFields_adds_many_fields_to_a_form()
    {
        $field_names = $this->getManyFieldNames(5);

        $Form = new Form();
        $Form->addFields($field_names);

        foreach($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $Form->getField($field_name));
        }
    }

    public function test_form_removeField_removes_a_field_from_the_form()
    {
        $field = $this->getFieldData();

        $Form = new Form();
        $Form->addField($field['name']);

        // Make sure it added properly
        $this->assertEquals($field['name'], $Form->getField($field['name'])->name);

        // Remove it
        $Form->removeField($field['name']);

        // Should not be set anymore
        $this->assertFalse(isset($Form->{$field['name']}));

        // Trying to get this field should also now throw an exception
        $this->expectException(InvalidFieldException::class);
        $Form->getField($field['name']);
    }

    public function test_form_removeFields_removes_many_fields_from_the_form()
    {
        $field_names = $this->getManyFieldNames(5);

        $Form = new Form();
        $Form->addFields($field_names);

        $remove = [$field_names[0], $field_names[2]];
        unset($field_names[0]);
        unset($field_names[2]);
        $Form->removeFields($remove);

        // The others still exist
        foreach($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $Form->{$field_name});
        }

        // The ones we removed do not exist
        foreach($remove as $field_name) {
            $this->assertFalse($Form->isField($field_name));
        }
    }

    public function test_form_isField_returns_existence_of_field()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->assertTrue($Form->isField('first'));
        $this->assertTrue($Form->isField('second'));
        $this->assertFalse($Form->isField('third'));
    }

    public function test_form_magic_set_method_will_set_field_value()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $Form->first = 'MyValue1234';
        $Form->second = 'DifferentValue4321';

        $this->assertEquals('MyValue1234', $Form->first->value);
        $this->assertEquals('DifferentValue4321', $Form->second->value);
    }

    public function test_form_magic_set_method_will_throw_exception_on_invalid_field()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->expectException(InvalidFieldException::class);
        $Form->third = 'broken';
    }

    public function test_form_magic_get_method_will_get_field_object()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $Form->{$field_names[0]});
    }

    public function test_form_magic_get_method_will_throw_exception_on_invalid_field()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->expectException(InvalidFieldException::class);
        $Field = $Form->third;
    }

    public function test_form_magic_isset_method_returns_existence_of_field()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $this->assertTrue(isset($Form->first));
        $this->assertTrue(isset($Form->{$field_names[1]}));
        $this->assertFalse(isset($Form->third));
    }

    public function test_form_getFieldValues_returns_all_field_values()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        // Set the values
        foreach($fields as $field){
            $Form->{$field['name']} = $field['value'];
        }

        $this->assertEquals(array_column($fields, 'value', 'name'), $Form->getFieldValues());
    }

    public function test_form_setValues_sets_multiple_field_values()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $Form->setValues(array_column($fields, 'value', 'name'));

        $this->assertEquals(array_column($fields, 'value', 'name'), $Form->getFieldValues());
    }

    public function test_form_setValues_throws_an_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $fields = ['not_a_real_field' => 1234];

        $this->expectException(InvalidFieldException::class);
        $Form->setValues($fields);
    }

    public function test_form_setValues_does_not_throw_an_exception_on_invalid_field_when_true_passed()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_values = ['not_a_real_field' => 1234, $fields[0]['name'] => $fields[0]['value']];
        $Form->setValues($field_values, true);

        $this->assertEquals($fields[0]['value'], $Form->{$fields[0]['name']}->value);
    }

    public function test_form_setDisplayFields_sets_multiple_fields_for_display()
    {
        $fields = $this->getFieldData(10);

        // Field names only (make sure there aren't duplicates)
        $field_names = array_column($fields, 'name');
        $field_names = array_combine($field_names, $field_names);

        // Create the form
        $Form = new Form();
        $Form->addFields($field_names);


        // Set all fields as display fields
        $Form->setDisplayFields($field_names);

        $this->assertEquals($field_names, $Form->getDisplayFields());

        // Remove one field
        $key = array_rand($field_names);
        $removed1 = [$field_names[$key]];
        unset($field_names[$key]);

        $Form->removeDisplayFields($removed1);
        $this->assertEquals($field_names, $Form->getDisplayFields());

        // Remove many fields
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

    public function test_form_setDisplayFields_overwrites_existing_display_fields()
    {
        $fields = $this->getFieldData(10);

        // Field names only (make sure there aren't duplicates)
        $field_names = array_column($fields, 'name');
        $field_names = array_combine($field_names, $field_names);

        // Create the form
        $Form = new Form();
        $Form->addFields($field_names);

        // Set all fields as display fields
        $Form->setDisplayFields($field_names);
        $this->assertEquals($field_names, $Form->getDisplayFields());

        // Take only a subset of fields and set those as display
        $field_names = array_slice($field_names, 2, 4, true);
        $Form->setDisplayFields($field_names);
        $this->assertEquals($field_names, $Form->getDisplayFields());
    }

    public function test_form_setLabels_sets_labels_on_multiple_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $labels = array_column($fields, 'label', 'name');

        $Form->setLabels($labels);
        $this->assertEquals($labels, $Form->getLabels());

        // Check individual fields too? why not...
        foreach(array_column($fields, 'name') as $field_name) {
            $this->assertEquals($labels[$field_name], $Form->getField($field_name)->label);
        }
    }

    public function test_form_setLabels_throws_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $labels = array_column($fields, 'label', 'name');
        $labels['not_a_valid_field'] = 'Some Label';

        $this->expectException(InvalidFieldException::class);
        $Form->setLabels($labels);
    }

    public function test_form_has_Attributes()
    {
        $this->assertClassHasAttribute('Attributes', Form::class);

        $Form = new Form();
        $this->assertInstanceOf(\Nickwest\EloquentForms\Attributes::class, $Form->Attributes);
    }

    public function test_form_Attributes_can_be_set()
    {
        $Form = new Form();

        $Form->Attributes->action = 'http://google.com';
        $this->assertEquals($Form->Attributes->action, 'http://google.com');
    }

    public function test_form_addDatalist_adds_a_datalist_to_the_form_and_sets_it_for_display()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $Form->addDataList('my_data', ['1' => 'one', '2' => 'two', '3' => 'three']);

        // The Field exists now
        $this->assertTrue(isset($Form->my_data));

        // It's in the display array
        $this->assertAttributeContains('my_data', 'display_fields', $Form);
    }


    public function test_form_setTheme_sets_the_theme_on_the_form()
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
    //     $fields = $this->getFieldData(5);

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
        foreach($this->getFieldData(5) as $field){
            $field_names[] = $field['name'];
        }

        return $field_names;
    }

    private function getFieldData(int $count = 1)
    {
        $Faker = Faker\Factory::create();

        $fields = [];
        for($i = 0; $i < $count; $i++){
            $fields[] = [
                'name' => $Faker->unique()->word,
                'length' => $Faker->numberBetween(10, 255),
                'default' => $Faker->name,
                'value' => $Faker->name,
                'type' => 'text',
                'label' => $Faker->state,
            ];
        }

        if($count == 1){
            return current($fields);
        }

        return $fields;
    }

}

?>
