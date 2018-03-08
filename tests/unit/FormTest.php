<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;
use Nickwest\EloquentForms\Exceptions\InvalidCustomFieldObjectException;

use Nickwest\EloquentForms\Test\TestCase;

class FormTest extends TestCase
{
    public function setUp()
    {
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
        $this->assertEquals($field['name'], $Form->getField($field['name'])->Attributes->name);

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

        $this->assertEquals('MyValue1234', $Form->first->Attributes->value);
        $this->assertEquals('DifferentValue4321', $Form->second->Attributes->value);
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

    public function test_form_addSubForm_adds_a_subform_to_the_form()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $SubForm = new Form();
        $SubForm->addFields(['sub1', 'sub2']);

        $Form->addSubForm('my_subform', $SubForm);

        $this->assertTrue(isset($Form->my_subform));
        $this->assertInstanceOf(\Nickwest\EloquentForms\Form::class, $Form->my_subform->Subform);
    }

    public function test_form_SubForm_fields_are_accessible()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        $SubForm = new Form();
        $SubForm->addFields(['sub1', 'sub2']);

        $Form->addSubForm('my_subform', $SubForm);

        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $Form->my_subform->Subform->sub1);
    }

    public function test_form_addSubForm_adds_form_before_specific_field()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);
        $Form->setDisplayFields($field_names);

        $SubForm = new Form();
        $SubForm->addFields(['sub1', 'sub2']);

        $Form->addSubForm('my_subform', $SubForm, 'second');

        // Make sure it's in the second spot, rather than the end
        $this->assertEquals('my_subform', current(array_slice($Form->getDisplayFields(), 1, 1)));
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

    public function test_form_setValue_sets_a_fields_value()
    {
        $Form = new Form();
        $Form->addFields(['test_field']);

        $Form->setValue('test_field', 'abc1234');

        $this->assertEquals('abc1234', $Form->test_field->Attributes->value);
    }

    public function test_form_setValue_throws_an_exception_on_invalid_field()
    {
        $Form = new Form();

        $this->expectException(InvalidFieldException::class);
        $Form->setValue('test_field', 'abc1234');
    }

    public function test_form_getValue_returns_a_field_value()
    {
        $Form = new Form();
        $Form->addFields(['test_field']);

        $Form->setValue('test_field', 'abc1234');

        $this->assertEquals('abc1234', $Form->getValue('test_field'));
    }

    public function test_form_getValue_throws_an_exception_on_invalid_field()
    {
        $Form = new Form();

        $this->expectException(InvalidFieldException::class);
        $value = $Form->getValue('test_field');
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

    public function test_form_setValues_does_not_throw_an_exception_on_invalid_field_when_ignoring_invalid_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_values = ['not_a_real_field' => 1234, $fields[0]['name'] => $fields[0]['value']];
        $Form->setValues($field_values, true);

        $this->assertEquals($fields[0]['value'], $Form->{$fields[0]['name']}->Attributes->value);
    }

    public function test_form_setNames_sets_field_name_attribute_on_multiple_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        // Change all the names to my_buttons
        $new_names = [];
        foreach($fields as $field){
            $new_names[$field['name']] = 'my_buttons';
        }

        $Form->setNames($new_names);

        foreach($Form->getFields() as $Field){
            $this->assertEquals('my_buttons', $Field->Attributes->name);
        }
    }

    public function test_form_setTypes_sets_multiple_field_types()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_types = [$fields[0]['name'] => 'checkbox', $fields[1]['name'] => 'textarea'];
        $Form->setTypes($field_types);

        $this->assertEquals($Form->{$fields[0]['name']}->Attributes->type, 'checkbox');
        $this->assertEquals($Form->{$fields[1]['name']}->Attributes->type, 'textarea');
    }

    public function test_form_setTypes_throws_an_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_types = ['not_a_real_field' => 'text', $fields[0]['name'] => 'checkbox'];

        $this->expectException(InvalidFieldException::class);
        $Form->setTypes($field_types);
    }

    public function test_form_setTypes_allows_CustomField_types_to_be_set()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $CustomType = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField();

        $field_types = [$fields[0]['name'] => 'checkbox', $fields[1]['name'] => 'textarea', $fields[2]['name'] => $CustomType];
        $Form->setTypes($field_types);

        $this->assertInstanceOf(\Nickwest\EloquentForms\CustomField::class, $Form->{$fields[2]['name']}->CustomField);
        $this->assertInstanceOf(\Nickwest\EloquentForms\CustomFields\daysofweek\CustomField::class, $Form->{$fields[2]['name']}->CustomField);
    }

    public function test_form_setTypes_throws_exception_if_object_is_not_CustomField()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $CustomType = new \StdClass;

        $field_types = [$fields[0]['name'] => 'checkbox', $fields[1]['name'] => 'textarea', $fields[2]['name'] => $CustomType];
        $this->expectException(InvalidCustomFieldObjectException::class);
        $Form->setTypes($field_types);
    }

    public function test_form_setExamples_sets_examples_on_multiple_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_examples = [$fields[0]['name'] => 'test@example.com', $fields[1]['name'] => '555-1212'];
        $Form->setExamples($field_examples);

        $this->assertEquals($Form->{$fields[0]['name']}->example, 'test@example.com');
        $this->assertEquals($Form->{$fields[1]['name']}->example, '555-1212');
    }

    public function test_form_setDefaultValues_sets_default_values_on_multiple_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $default_values = array_column($fields, 'default_value', 'name');

        $Form->setDefaultValues($default_values);

        foreach(array_column($fields, 'name') as $field_name) {
            $this->assertEquals($default_values[$field_name], $Form->getField($field_name)->default_value);
        }
    }

    public function test_form_setDefaultValues_throws_an_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $default_values = array_column($fields, 'default_value', 'name');
        $default_values['not_a_real_field'] = 'Blah blahh';

        $this->expectException(InvalidFieldException::class);
        $Form->setDefaultValues($default_values);
    }

    public function test_form_setRequiredFields_sets_required_attribute_on_fields()
    {
        $fields = $this->getFieldData(10);
        $field_names = array_slice(array_column($fields, 'name'), 0, 3);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        // Make sure they start out as Null
        foreach(array_column($fields, 'name') as $field_name) {
            $this->assertFalse(isset($Form->{$field_name}->Attributes->required));
        }

        // Set required
        $Form->setRequiredFields($field_names);

        // Make sure they gained the required attribute, and it's set to true
        foreach($field_names as $field_name) {
            $this->assertTrue(isset($Form->{$field_name}->Attributes->required));
        }
    }

    public function test_form_setRequiredFields_throws_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(10);
        $field_names = array_slice(array_column($fields, 'name'), 0, 3);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_names[] = 'not_a_real_field';

        $this->expectException(InvalidFieldException::class);
        $Form->setRequiredFields($field_names);
    }

    public function test_form_setInline_sets_multiple_fields_to_inline()
    {
        $fields = $this->getFieldData(10);
        $field_names = array_slice(array_column($fields, 'name'), 0, 3);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $Form->setInline($field_names);

        // Make sure they gained the inline, and it's set to true
        foreach($field_names as $field_name) {
            $this->assertTrue($Form->{$field_name}->is_inline);
        }
    }

    public function test_form_setInline_throws_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(10);
        $field_names = array_slice(array_column($fields, 'name'), 0, 3);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $field_names[] = 'not_a_real_field';

        $this->expectException(InvalidFieldException::class);
        $Form->setInline($field_names);
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

    public function test_form_setValidationRules_adds_rules_to_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $validation_rules = [
            $fields[0]['name'] => 'required,date,after:tomorrow',
            $fields[2]['name'] => 'exists:connection.staff,email',
            $fields[4]['name'] => 'exists:connection.staff,image',
        ];

        $Form->setValidationRules($validation_rules);

        foreach(array_column($fields, 'name') as $field_name) {
            if(isset($validation_rules[$field_name])){
                $this->assertEquals($validation_rules[$field_name], $Form->getField($field_name)->validation_rules);
            }
        }
    }

    public function test_form_setValidationRules_throws_exception_on_invalid_field()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $validation_rules = [
            $fields[0]['name'] => 'required|date|after:tomorrow',
            $fields[2]['name'] => 'exists:connection.staff|email',
            $fields[4]['name'] => 'exists:connection.staff|image',
            'not_valid_field' => 'required',
        ];

        $this->expectException(InvalidFieldException::class);
        $Form->setValidationRules($validation_rules);
    }

    public function test_form_isValid_can_pass_validation()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $validation_rules = [
            $fields[0]['name'] => 'required|date|after:tomorrow',
            $fields[2]['name'] => 'email',
            $fields[4]['name'] => 'required|email',
        ];

        $Form->{$fields[0]['name']}->Attributes->value = '2120-05-10';
        $Form->{$fields[2]['name']}->Attributes->value = 'test@test.com';
        $Form->{$fields[4]['name']}->Attributes->value = 'test2@test.com';

        $Form->setValidationRules($validation_rules);

        $this->assertTrue($Form->isValid());
    }

    public function test_form_isValid_can_fail_validation()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $validation_rules = [
            $fields[0]['name'] => 'required|date|after:tomorrow',
            $fields[2]['name'] => 'email',
            $fields[4]['name'] => 'required|email',
        ];

        $Form->{$fields[0]['name']}->Attributes->value = '2120-05-10';
        $Form->{$fields[2]['name']}->Attributes->value = 'test@test.com';

        $Form->setValidationRules($validation_rules);

        $this->assertFalse($Form->isValid());
    }

    public function test_form_isValid_sets_errors_on_invalid_fields()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $validation_rules = [
            $fields[0]['name'] => 'required|date|after:tomorrow',
            $fields[2]['name'] => 'email',
            $fields[4]['name'] => 'required|email',
        ];

        $Form->{$fields[0]['name']}->Attributes->value = '2120-05-10'; // Valid
        $Form->{$fields[2]['name']}->Attributes->value = 'testcom'; // invalid

        $Form->setValidationRules($validation_rules);
        $Form->isValid();

        $this->assertNotEmpty($Form->{$fields[2]['name']}->error_message);
        $this->assertNotEmpty($Form->{$fields[4]['name']}->error_message);
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

    public function test_form_starts_with_a_save_submit_button()
    {
        $Form = new Form();

        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $Form->getSubmitButton('submit'));
        $this->assertArrayHasKey('submit', $Form->getSubmitButtons());
    }


    public function test_form_addSubmitButton_adds_a_submit_button()
    {
        $Form = new Form();

        $Form->addSubmitButton('resubmit', 'Resubmit', 'is-warning');

        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $Form->getSubmitButton('resubmit'));
        $this->assertArrayHasKey('resubmit', $Form->getSubmitButtons());
    }

    public function test_form_removeSubmitButton_removes_a_submit_button()
    {
        $Form = new Form();

        $Form->addSubmitButton('resubmit', 'Resubmit', 'is-warning');
        $Form->removeSubmitButton('submit');

        $this->expectException(InvalidFieldException::class);
        $Field = $Form->getSubmitButton('submit');
    }

    public function test_form_removeSubmitButton_throws_an_exception_when_invalid_name_passed()
    {
        $Form = new Form();

        $this->expectException(InvalidFieldException::class);
        $Field = $Form->getSubmitButton('not_a_button');
    }

    public function test_form_getSubmitButton_gets_a_submit_button()
    {
        $Form = new Form();

        // Test against the default submit button
        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $Form->getSubmitButton('submit'));
    }

    public function test_form_getSubmitButton_throws_exception_when_invalid_field_name()
    {
        $Form = new Form();

        $this->expectException(InvalidFieldException::class);
        $Form->getSubmitButton('no_a_field_valid');
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

    public function test_form_toJson_creates_valid_json()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        // Add a subform
        $subForm = new Form();
        $subForm->addFields(['sub1', 'sub2']);

        $Form->addSubform('my_subby', $subForm);

        $json = $Form->toJson();

        $this->assertJson($json);
    }

    public function test_form_toJson_converts_a_form_to_json_and_back_again()
    {
        $field_names = ['first', 'second'];

        $Form = new Form();
        $Form->addFields($field_names);

        // Add a subform
        $subForm = new Form();
        $subForm->addFields(['sub1', 'sub2']);

        $Form->addSubform('my_subby', $subForm);

        $json = $Form->toJson();

        $newForm = new Form;
        $newForm->fromJson($json);

        $this->assertEquals($Form, $newForm);

    }

    public function test_form_can_make_a_view_without_breaking()
    {
        $fields = $this->getFieldData(5);

        $Form = new Form();
        $Form->addFields(array_column($fields, 'name'));

        $view = $Form->MakeView();
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
    }





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
                'default_value' => $Faker->name,
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
