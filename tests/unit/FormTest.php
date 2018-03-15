<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Illuminate\Validation\Rule;

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

        $this->fields = [];
        $this->sub_fields = [];
        $this->validation_rules = [];

        $this->Faker = Faker\Factory::create();

        $this->Form = $this->createComplexForm();
    }


    public function test_form_addField_adds_a_field_to_the_form()
    {
        // Add the field
        $this->Form->addField('my_ultra_unique_name');
        $this->assertInstanceOf(Field::class, $this->Form->getField('my_ultra_unique_name'));
    }

    public function test_form_getField_throws_exception_on_invalid_field()
    {
        // Make sure there's no such field already and we get the proper exception
        $this->expectException(InvalidFieldException::class);
        $this->Form->getField('another_unique_name');
    }

    public function test_form_addFields_adds_many_fields_to_a_form()
    {
        $field_names = $this->getManyFieldNames(5);

        $this->Form->addFields($field_names);

        foreach($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $this->Form->getField($field_name));
        }
    }

    public function test_form_removeField_removes_a_field_from_the_form()
    {
        $field_name = current($this->fields)['name'];

        // Remove it
        $this->Form->removeField($field_name);

        // Should not be set anymore
        $this->assertFalse(isset($this->Form->{$field_name}));

        // Trying to get this field should also now throw an exception
        $this->expectException(InvalidFieldException::class);
        $this->Form->getField($field_name);
    }

    public function test_form_removeFields_removes_many_fields_from_the_form()
    {
        foreach(array_rand($this->fields, 3) as $key){
            $remove[] = $this->fields[$key]['name'];
            unset($this->fields[$key]);
        }

        $this->Form->removeFields($remove);

        // The others still exist
        foreach($this->fields as $field) {
            $this->assertInstanceOf(Field::class, $this->Form->{$field['name']});
        }

        // The ones we removed do not exist
        foreach($remove as $field_name) {
            $this->assertFalse($this->Form->isField($field_name));
        }
    }

    public function test_form_isField_returns_existence_of_field()
    {
        foreach($this->fields as $field){
            $this->assertTrue($this->Form->isField($field['name']));
        }

        $this->assertFalse($this->Form->isField('some_field_that_does_not_exist'));
    }

    public function test_form_magic_set_method_will_set_field_value()
    {
        foreach($this->fields as $field){
            $this->Form->{$field['name']} = $field['value'];
            $this->assertEquals($field['value'], $this->Form->{$field['name']}->attributes->value);
        }
    }

    public function test_form_magic_set_method_will_throw_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->notafieldnameatall = 'broken';
    }

    public function test_form_magic_get_method_will_get_field_object()
    {
        foreach($this->fields as $field){
            $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->{$field['name']});
        }
    }

    public function test_form_magic_get_method_will_throw_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $Field = $this->Form->notafieldnameatall;
    }

    public function test_form_magic_isset_method_returns_existence_of_field()
    {
        foreach($this->fields as $field){
            $this->assertTrue(isset($this->Form->{$field['name']}));
        }
        $this->assertFalse(isset($this->Form->notafieldnameatall));
    }

    public function test_form_addSubForm_adds_a_subform_to_the_form()
    {
        $SubForm = new Form();
        $SubForm->addFields(['sub1', 'sub2']);

        $this->Form->addSubForm('another_subform', $SubForm);

        $this->assertTrue(isset($this->Form->another_subform));
        $this->assertInstanceOf(\Nickwest\EloquentForms\Form::class, $this->Form->another_subform->Subform);
    }

    public function test_form_SubForm_fields_are_accessible()
    {
        foreach($this->sub_fields as $field){
            $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->test_subform->Subform->{$field['name']});
        }
    }

    public function test_form_addSubForm_adds_form_before_specific_field()
    {
        $SubForm = new Form();
        $SubForm->addFields(['sub1', 'sub2']);

        $this->Form->addSubForm('another_subform', $SubForm, $this->fields[4]['name']);

        // Make sure it's in the second spot, rather than the end
        $this->assertEquals('another_subform', current(array_slice($this->Form->getDisplayFieldNames(), 4, 1)));
    }

    public function test_form_getFieldValues_returns_all_field_values()
    {
        $this->assertEquals(array_column($this->fields, 'value', 'name'), $this->Form->getFieldValues());
    }

    public function test_form_setValue_sets_a_fields_value()
    {
        // Set all fields to a simple value
        foreach($this->fields as $field){
            $this->Form->setValue($field['name'], 'abc1234');
            $this->assertEquals('abc1234', $this->Form->{$field['name']}->attributes->value);
        }
    }

    public function test_form_setValue_throws_an_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->setValue('not_a_real_field_at_all', 'abc1234');
    }

    public function test_form_getValue_returns_a_field_value()
    {

        foreach($this->fields as $field){
            $this->assertEquals($field['value'], $this->Form->getValue($field['name']));
        }
    }

    public function test_form_getValue_throws_an_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $value = $this->Form->getValue('a_field_that_does_not_exist');
    }

    public function test_form_setValues_sets_multiple_field_values()
    {
        // Set all fields to a simple value
        $values = array_column($this->fields, 'value', 'name');
        foreach($values as $key => $value){
            $values[$key] = 'zyx4321';
        }

        $this->Form->setValues($values);

        $this->assertEquals($values, $this->Form->getFieldValues());
    }

    public function test_form_setValues_throws_an_exception_on_invalid_field()
    {
        $values = array_column($this->fields, 'value', 'name');
        $values['not_a_field'] = 'break!';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setValues($values);
    }

    public function test_form_setValues_does_not_throw_an_exception_on_invalid_field_when_ignoring_invalid_fields()
    {
        $values = array_column($this->fields, 'value', 'name');
        foreach($values as $key => $value){
            $values[$key] = 'zyx4321';
        }
        $values['not_a_field'] = 'break!';

        $this->Form->setValues($values, true);

        $this->assertEquals('zyx4321', $this->Form->{$this->fields[0]['name']}->attributes->value);
    }

    public function test_form_setNames_sets_field_name_attribute_on_multiple_fields()
    {
        // Change all the names to my_buttons
        $new_names = [];
        foreach($this->fields as $field){
            $new_names[$field['name']] = 'my_buttons';
        }
        // add the subform, we want to rename it too I guess
        $new_names['test_subform'] = 'my_buttons';

        $this->Form->setNames($new_names);

        foreach($this->Form->getFields() as $Field){
            $this->assertEquals('my_buttons', $Field->attributes->name);
        }
    }

    public function test_form_setTypes_sets_multiple_field_types()
    {
        $field_types = [$this->fields[0]['name'] => 'checkbox', $this->fields[1]['name'] => 'textarea'];
        $this->Form->setTypes($field_types);

        $this->assertEquals($this->Form->{$this->fields[0]['name']}->attributes->type, 'checkbox');
        $this->assertEquals($this->Form->{$this->fields[1]['name']}->attributes->type, 'textarea');
    }

    public function test_form_setTypes_throws_an_exception_on_invalid_field()
    {
        $field_types = ['not_a_real_field' => 'text', $this->fields[0]['name'] => 'checkbox'];

        $this->expectException(InvalidFieldException::class);
        $this->Form->setTypes($field_types);
    }

    public function test_form_setTypes_allows_CustomField_types_to_be_set()
    {
        $CustomType = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField();

        $field_types = [$this->fields[0]['name'] => 'checkbox', $this->fields[1]['name'] => 'textarea', $this->fields[2]['name'] => $CustomType];
        $this->Form->setTypes($field_types);

        $this->assertInstanceOf(\Nickwest\EloquentForms\CustomField::class, $this->Form->{$this->fields[2]['name']}->CustomField);
        $this->assertInstanceOf(\Nickwest\EloquentForms\CustomFields\daysofweek\CustomField::class, $this->Form->{$this->fields[2]['name']}->CustomField);
    }

    public function test_form_setTypes_throws_exception_if_object_is_not_CustomField()
    {
        $CustomType = new \StdClass;

        $field_types = [$this->fields[0]['name'] => 'checkbox', $this->fields[1]['name'] => 'textarea', $this->fields[2]['name'] => $CustomType];
        $this->expectException(InvalidCustomFieldObjectException::class);
        $this->Form->setTypes($field_types);
    }

    public function test_form_setExamples_sets_examples_on_multiple_fields()
    {
        $field_examples = [$this->fields[0]['name'] => 'test@example.com', $this->fields[1]['name'] => '555-1212'];
        $this->Form->setExamples($field_examples);

        $this->assertEquals($this->Form->{$this->fields[0]['name']}->example, 'test@example.com');
        $this->assertEquals($this->Form->{$this->fields[1]['name']}->example, '555-1212');
    }

    public function test_form_setDefaultValues_sets_default_values_on_multiple_fields()
    {
        $default_values = array_column($this->fields, 'default_value', 'name');

        $this->Form->setDefaultValues($default_values);

        foreach(array_column($this->fields, 'name') as $field_name) {
            $this->assertEquals($default_values[$field_name], $this->Form->getField($field_name)->default_value);
        }
    }

    public function test_form_setDefaultValues_throws_an_exception_on_invalid_field()
    {
        $default_values = array_column($this->fields, 'default_value', 'name');
        $default_values['not_a_real_field'] = 'Blah blahh';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setDefaultValues($default_values);
    }

    public function test_form_setRequiredFields_sets_required_attribute_on_fields()
    {
        // Make sure they start out as Null
        foreach($this->fields as $field) {
            $this->assertFalse(isset($this->Form->{$field['name']}->attributes->required));
        }

        // Set required
        $this->Form->setRequiredFields(array_column($this->fields, 'name'));

        // Make sure they gained the required attribute, and it's set to true
        foreach($this->fields as $field) {
            $this->assertTrue(isset($this->Form->{$field['name']}->attributes->required));
        }
    }

    public function test_form_setRequiredFields_throws_exception_on_invalid_field()
    {
        $field_names = array_slice(array_column($this->fields, 'name'), 0, 3);

        $field_names[] = 'not_a_real_field';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setRequiredFields($field_names);
    }

    public function test_form_setInline_sets_multiple_fields_to_inline()
    {
        $field_names = array_slice(array_column($this->fields, 'name'), 0, 3);

        $this->Form->setInline($field_names);

        // Make sure they gained the inline, and it's set to true
        foreach($field_names as $field_name) {
            $this->assertTrue($this->Form->{$field_name}->is_inline);
        }
    }

    public function test_form_setInline_throws_exception_on_invalid_field()
    {
        $field_names = array_slice(array_column($this->fields, 'name'), 0, 3);

        $field_names[] = 'not_a_real_field';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setInline($field_names);
    }


    public function test_form_setDisplayFields_sets_multiple_fields_for_display()
    {
        // Field names only (make sure there aren't duplicates)
        $field_names = array_column($this->fields, 'name');
        $field_names = array_combine($field_names, $field_names);

        // Empty out the display fields
        $this->Form->setDisplayFields([]);
        $this->assertEquals([], $this->Form->getDisplayFieldNames());

        // Set all fields as display fields
        $this->Form->setDisplayFields($field_names);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());

        // Remove one field
        $key = array_rand($field_names);
        $removed1 = [$field_names[$key]];
        unset($field_names[$key]);

        $this->Form->removeDisplayFields($removed1);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());

        // Remove many fields
        $keys = array_rand($field_names, 3);
        $removed = [];
        foreach($keys as $key){
            $removed[] = $field_names[$key];
            unset($field_names[$key]);
        }

        $this->Form->removeDisplayFields($removed);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());

        // Add the last fields we removed back in
        $this->Form->addDisplayFields($removed);
        $field_names = array_merge($field_names, array_combine($removed, $removed));
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());

        // Inject the first field we removed back in after the 3rd
        $key = current(array_slice($field_names, 3, 1));
        $this->Form->setDisplayAfter(current($removed1), $field_names[$key]);
        $field_names = array_slice($field_names, 0, 3, true) + [current($removed1) => current($removed1)] + array_slice($field_names, 3, null, true);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());
    }

    public function test_form_setDisplayFields_overwrites_existing_display_fields()
    {
        // Field names only (make sure there aren't duplicates)
        $field_names = array_column($this->fields, 'name');
        $field_names = array_combine($field_names, $field_names);

        // Set all fields as display fields
        $this->Form->setDisplayFields($field_names);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());

        // Take only a subset of fields and set those as display
        $field_names = array_slice($field_names, 2, 4, true);
        $this->Form->setDisplayFields($field_names);
        $this->assertEquals($field_names, $this->Form->getDisplayFieldNames());
    }

    public function test_form_setLabels_sets_labels_on_multiple_fields()
    {
        $labels = array_column($this->fields, 'label', 'name');
        $labels['test_subform'] = 'SubForm!';

        $this->Form->setLabels($labels);
        $this->assertEquals($labels, $this->Form->getLabels());

        // Check individual fields too? why not...
        foreach(array_column($this->fields, 'name') as $field_name) {
            $this->assertEquals($labels[$field_name], $this->Form->getField($field_name)->label);
        }
    }

    public function test_form_setLabels_throws_exception_on_invalid_field()
    {
        $labels = array_column($this->fields, 'label', 'name');
        $labels['not_a_valid_field'] = 'Some Label';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setLabels($labels);
    }

    public function test_form_setValidationRules_adds_rules_to_fields()
    {
        // Change some validation rules
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required,date,after:tomorrow',
            $this->fields[7]['name'] => 'exists:connection.staff,email',
            $this->fields[9]['name'] => 'exists:connection.staff,image',
        ];

        $this->Form->setValidationRules($this->validation_rules);

        foreach(array_column($this->fields, 'name') as $field_name) {
            if(isset($this->validation_rules[$field_name])){
                $this->assertEquals($this->validation_rules[$field_name], $this->Form->getField($field_name)->validation_rules);
            }
        }
    }

    public function test_form_getValidationn_rules_returns_same_rules_that_are_set()
    {
        // Change some validation rules
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required,date,after:tomorrow',
            $this->fields[7]['name'] => 'exists:connection.staff,email',
            $this->fields[9]['name'] => 'exists:connection.staff,image',
        ];

        $this->Form->setValidationRules($this->validation_rules);

        $form_validation_rules = $this->Form->getValidationRules();

        $this->assertEquals($this->validation_rules, $form_validation_rules);

    }

    public function test_form_setValidationRules_throws_exception_on_invalid_field()
    {
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required|date|after:tomorrow',
            $this->fields[7]['name'] => 'exists:connection.staff|email',
            $this->fields[9]['name'] => 'exists:connection.staff|image',
            'not_valid_field' => 'required',
        ];

        $this->expectException(InvalidFieldException::class);
        $this->Form->setValidationRules($this->validation_rules);
    }

    public function test_form_isValid_can_pass_validation()
    {
        // Check that it's valid before we change rules
        $this->assertTrue($this->Form->isValid());

        // Change rules
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required|date|after:tomorrow',
            $this->fields[7]['name'] => 'email',
            $this->fields[9]['name'] => 'required|email',
        ];

        // Set some values so it paasses
        $this->Form->{$this->fields[6]['name']}->attributes->value = '2120-05-10';
        $this->Form->{$this->fields[7]['name']}->attributes->value = '';
        $this->Form->{$this->fields[9]['name']}->attributes->value = 'test2@test.com';

        $this->Form->setValidationRules($this->validation_rules);

        $this->assertTrue($this->Form->isValid());
    }

    public function test_form_isValid_can_fail_validation()
    {
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required|date|after:tomorrow',
            $this->fields[7]['name'] => 'email',
            $this->fields[9]['name'] => 'required|email',
        ];

        $this->Form->{$this->fields[6]['name']}->attributes->value = '2120-05-10';
        $this->Form->{$this->fields[7]['name']}->attributes->value = 'testtest.com';
        $this->Form->{$this->fields[9]['name']}->attributes->value = 'test@test.com';

        $this->Form->setValidationRules($this->validation_rules);

        $this->assertFalse($this->Form->isValid());
    }

    public function test_form_isValid_sets_errors_on_invalid_fields()
    {
        $this->validation_rules += [
            $this->fields[6]['name'] => 'required|date|after:tomorrow',
            $this->fields[7]['name'] => 'email',
            $this->fields[9]['name'] => 'required|email',
        ];

        $this->Form->{$this->fields[6]['name']}->attributes->value = '2120-05-10'; // Valid
        $this->Form->{$this->fields[7]['name']}->attributes->value = 'testcom'; // invalid
        $this->Form->{$this->fields[9]['name']}->attributes->value = ''; // invalid

        $this->Form->setValidationRules($this->validation_rules);
        $this->assertFalse($this->Form->isValid());

        // Make sure messages are set
        $this->assertEmpty($this->Form->{$this->fields[6]['name']}->error_message);
        $this->assertNotEmpty($this->Form->{$this->fields[7]['name']}->error_message);
        $this->assertNotEmpty($this->Form->{$this->fields[9]['name']}->error_message);
    }


    public function test_form_has_Attributes()
    {
        $this->assertClassHasAttribute('attributes', Form::class);

        $this->assertInstanceOf(\Nickwest\EloquentForms\Attributes::class, $this->Form->attributes);
    }

    public function test_form_Attributes_can_be_set_they_are_public()
    {
        $this->Form->attributes->action = 'http://google.com';
        $this->assertEquals($this->Form->attributes->action, 'http://google.com');
    }

    public function test_form_addDatalist_adds_a_datalist_to_the_form_and_sets_it_for_display()
    {
        $data = ['1' => 'one', '2' => 'two', '3' => 'three'];
        $this->Form->addDataList('my_data', $data);

        // The Field exists now
        $this->assertTrue(isset($this->Form->my_data));

        // It's in the display array
        $this->assertAttributeContains('my_data', 'display_fields', $this->Form);

        // Make sure they got set accurately too
        $this->assertEquals($data, $this->Form->my_data->getOptions());
    }

    public function test_form_starts_with_a_save_submit_button()
    {
        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->getSubmitButton('submit_button', 'Submit'));
        $this->assertArrayHasKey('submit_buttonSubmit', $this->Form->getSubmitButtons());
    }


    public function test_form_addSubmitButton_adds_a_submit_button()
    {
        $this->Form->addSubmitButton('resubmit_button', 'Resubmit', 'is-warning');

        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->getSubmitButton('resubmit_button', 'Resubmit'));
        $this->assertArrayHasKey('resubmit_buttonResubmit', $this->Form->getSubmitButtons());
    }

    public function test_form_removeSubmitButton_removes_a_submit_button()
    {
        $this->Form->addSubmitButton('resubmit_button', 'Resubmit', 'is-warning');
        $this->Form->removeSubmitButton('submit_button', 'Submit');

        $this->expectException(InvalidFieldException::class);
        $Field = $this->Form->getSubmitButton('submit_button', 'Submit');
    }

    public function test_form_removeSubmitButton_throws_an_exception_when_invalid_name_passed()
    {
        $this->expectException(InvalidFieldException::class);
        $Field = $this->Form->getSubmitButton('not_a_button', 'some_value');
    }

    public function test_form_getSubmitButton_gets_a_submit_button()
    {
        // Test against the default submit button
        $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->getSubmitButton('submit_button', 'Submit'));
    }

    public function test_form_getSubmitButton_throws_exception_when_invalid_field_name()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->getSubmitButton('not_a_field_valid', 'some_value');
    }

    public function test_form_renameSubmitButton_renames_the_button()
    {
        $this->Form->renameSubmitButton('submit_button', 'Submit', 'save_button', 'Save');

        $this->assertArrayHasKey('save_buttonSave', $this->Form->getSubmitButtons());

        $button = $this->Form->getSubmitButton('save_button', 'Save');

        $this->assertEquals('Save', $button->attributes->value);
        $this->assertEquals('save_button', $button->attributes->name);
    }

    public function test_form_renameSubmitButton_throws_exception_when_invalid_field_name()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->renameSubmitButton('not_a_field_valid', 'Submit', 'new_field_name');
    }

    public function test_form_renameSubmitButton_throws_exception_when_new_field_name_already_taken()
    {
        $this->Form->addSubmitButton('resubmit_button', 'Resubmit', 'is-warning');

        $this->expectException(InvalidFieldException::class);
        $this->Form->renameSubmitButton('submit_button', 'Submit', 'resubmit_button', 'Resubmit');
    }

    public function test_form_setTheme_sets_the_theme_on_the_form()
    {
        // Verify it starts with the Default theme set
        $this->assertInstanceOf(\Nickwest\EloquentForms\DefaultTheme::class, $this->Form->getTheme());

        $myTheme = new \Nickwest\EloquentForms\Themes\bulma\Theme();
        $this->Form->setTheme($myTheme);

        $this->assertInstanceOf(\Nickwest\EloquentForms\Themes\bulma\Theme::class, $this->Form->getTheme());
    }

    public function test_form_toJson_creates_valid_json()
    {
        // Add a subform
        $subForm = new Form();
        $subForm->addFields(['sub1', 'sub2']);

        $this->Form->addSubform('another_subform', $subForm);

        $json = $this->Form->toJson();

        $this->assertJson($json);
    }

    public function test_form_toJson_converts_a_form_to_json_and_back_again()
    {
        $json = $this->Form->toJson();

        $newForm = new Form;
        $newForm->fromJson($json);

        $this->assertEquals($this->Form, $newForm);

    }

    public function test_form_can_make_a_view_without_breaking()
    {
        $view = $this->Form->MakeView();
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
    }

    public function test_form_can_make_a_view_and_render_without_breaking()
    {
        $view = $this->Form->MakeView();
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);

        $view->render();
    }

    public function test_form_can_make_a_view_and_render_without_breaking_bulma_theme()
    {
        $this->Form->setTheme(new \Nickwest\EloquentForms\Themes\bulma\Theme());
        $view = $this->Form->MakeView();
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);

        $view->render();
    }



///// Test Setup helpers

    private function createComplexForm()
    {

        $this->fields = $this->getFieldData(15);

        $Form = new Form();
        $Form->addFields(array_column($this->fields, 'name'));

        $Form->{$this->fields[3]['name']}->setOptions(['Yes', 'No']);

        // Set some validation rules
        $this->validation_rules = [
            $this->fields[0]['name'] => 'required|date|after:tomorrow',
            $this->fields[1]['name'] => 'email',
            $this->fields[2]['name'] => 'required|email',
            $this->fields[3]['name'] => [
                'required',
                Rule::in($Form->{$this->fields[3]['name']}->getOptions()),
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

}

?>
