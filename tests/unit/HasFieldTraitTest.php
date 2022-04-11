<?php

namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;

use Nickwest\EloquentForms\Test\TestCase;

class HasFieldTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->fields = [];
        $this->sub_fields = [];
        $this->validation_rules = [];

        $this->Faker = Faker\Factory::create();

        $this->Form = $this->createComplexForm();
    }

    public function test_addField_adds_a_field_to_the_form()
    {
        // Add the field
        $this->Form->addField('my_ultra_unique_name');
        $this->assertInstanceOf(Field::class, $this->Form->getField('my_ultra_unique_name'));
    }

    public function test_getField_throws_exception_on_invalid_field()
    {
        // Make sure there's no such field already and we get the proper exception
        $this->expectException(InvalidFieldException::class);
        $this->Form->getField('another_unique_name');
    }

    public function test_addFields_adds_many_fields_to_a_form()
    {
        $field_names = $this->getManyFieldNames(5);

        $this->Form->addFields($field_names);

        foreach ($field_names as $field_name) {
            $this->assertInstanceOf(Field::class, $this->Form->getField($field_name));
        }
    }

    public function test_removeField_removes_a_field_from_the_form()
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

    public function test_removeFields_removes_many_fields_from_the_form()
    {
        foreach (array_rand($this->fields, 3) as $key) {
            $remove[] = $this->fields[$key]['name'];
            unset($this->fields[$key]);
        }

        $this->Form->removeFields($remove);

        // The others still exist
        foreach ($this->fields as $field) {
            $this->assertInstanceOf(Field::class, $this->Form->{$field['name']});
        }

        // The ones we removed do not exist
        foreach ($remove as $field_name) {
            $this->assertFalse($this->Form->isField($field_name));
        }
    }

    public function test_isField_returns_existence_of_field()
    {
        foreach ($this->fields as $field) {
            $this->assertTrue($this->Form->isField($field['name']));
        }

        $this->assertFalse($this->Form->isField('some_field_that_does_not_exist'));
    }

    public function test_magic_set_method_will_set_field_value()
    {
        foreach ($this->fields as $field) {
            $this->Form->{$field['name']} = $field['value'];
            $this->assertEquals($field['value'], $this->Form->{$field['name']}->attributes->value);
        }
    }

    public function test_magic_set_method_will_throw_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->notafieldnameatall = 'broken';
    }

    public function test_magic_get_method_will_get_field_object()
    {
        foreach ($this->fields as $field) {
            $this->assertInstanceOf(\Nickwest\EloquentForms\Field::class, $this->Form->{$field['name']});
        }
    }

    public function test_magic_get_method_will_throw_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $Field = $this->Form->notafieldnameatall;
    }

    public function test_magic_isset_method_returns_existence_of_field()
    {
        foreach ($this->fields as $field) {
            $this->assertTrue(isset($this->Form->{$field['name']}));
        }
        $this->assertFalse(isset($this->Form->notafieldnameatall));
    }

    public function test_getFieldValues_returns_all_field_values()
    {
        $this->assertEquals(array_column($this->fields, 'value', 'name'), $this->Form->getFieldValues());
    }

    public function test_setValue_sets_a_fields_value()
    {
        // Set all fields to a simple value
        foreach ($this->fields as $field) {
            $this->Form->setValue($field['name'], 'abc1234');
            $this->assertEquals('abc1234', $this->Form->{$field['name']}->attributes->value);
        }
    }

    public function test_setValue_throws_an_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $this->Form->setValue('not_a_real_field_at_all', 'abc1234');
    }

    public function test_getValue_returns_a_field_value()
    {

        foreach ($this->fields as $field) {
            $this->assertEquals($field['value'], $this->Form->getValue($field['name']));
        }
    }

    public function test_getValue_throws_an_exception_on_invalid_field()
    {
        $this->expectException(InvalidFieldException::class);
        $value = $this->Form->getValue('a_field_that_does_not_exist');
    }

    public function test_setValues_sets_multiple_field_values()
    {
        // Set all fields to a simple value
        $values = array_column($this->fields, 'value', 'name');
        foreach ($values as $key => $value) {
            $values[$key] = 'zyx4321';
        }

        $this->Form->setValues($values);

        $this->assertEquals($values, $this->Form->getFieldValues());
    }

    public function test_setValues_throws_an_exception_on_invalid_field()
    {
        $values = array_column($this->fields, 'value', 'name');
        $values['not_a_field'] = 'break!';

        $this->expectException(InvalidFieldException::class);
        $this->Form->setValues($values);
    }

    public function test_setValues_does_not_throw_an_exception_on_invalid_field_when_ignoring_invalid_fields()
    {
        $values = array_column($this->fields, 'value', 'name');
        foreach ($values as $key => $value) {
            $values[$key] = 'zyx4321';
        }
        $values['not_a_field'] = 'break!';

        $this->Form->setValues($values, true);

        $this->assertEquals('zyx4321', $this->Form->{$this->fields[0]['name']}->attributes->value);
    }
}
