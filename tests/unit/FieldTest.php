<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;

use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Exceptions\OptionValueException;

class FieldTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->Field = $this->getComplexField();
        $this->SubformField = $this->getSubformField();
    }

    public function test_field_has_some_stuff_set_on_construct()
    {
        $this->assertEquals('my_field', $this->Field->attributes->name);
        $this->assertEquals('text', $this->Field->attributes->type);
        $this->assertEquals('input-my_field', $this->Field->attributes->id);

        $this->assertEquals('my_field', $this->Field->getOriginalName());
        $this->assertEquals('my_field', $this->Field->getOriginalId());
        $this->assertEquals('My field', $this->Field->label);
    }

    public function test_field_getViewNamespace_returns_correct_name()
    {
        // When it's a default theme
        $this->assertEquals('Nickwest\\EloquentForms', $this->Field->getViewNamespace());

        // When we apply Bulma
        $this->Field->setTheme(new \Nickwest\EloquentForms\Themes\bulma\Theme());
        $this->assertEquals('Nickwest\\EloquentForms\\bulma', $this->Field->getViewNamespace());
    }

    public function test_field_isSubform_returns_if_field_is_a_subform()
    {
        $this->assertTrue($this->SubformField->isSubform());
    }

    public function test_field_getTemplate_returns_the_correct_template_for_fields()
    {
        $this->Field->attributes->type = 'textarea';
        $this->assertEquals('Nickwest\EloquentForms::fields.textarea', $this->Field->getTemplate());

        $this->Field->attributes->type = 'select';
        $this->assertEquals('Nickwest\EloquentForms::fields.select', $this->Field->getTemplate());

        // And with a different theme
        $this->Field->setTheme(new \Nickwest\EloquentForms\Themes\bulma\Theme());
        $this->assertEquals('Nickwest\EloquentForms\\bulma::fields.select', $this->Field->getTemplate());
    }

    public function test_field_getFormattedValue_works_with_options()
    {
        $this->Field->attributes->type = 'checkbox';
        $this->Field->attributes->value = 5;

        $options = ['key' => 'value', '5' => 'May'];
        $this->Field->options->setOptions($options);

        $this->assertEquals('May', $this->Field->getFormattedValue());

        $this->Field->attributes->value = 'key';
        $this->assertEquals('value', $this->Field->getFormattedValue());
    }

    public function test_field_getFormattedValue_works_without_options()
    {
        $this->Field->attributes->type = 'checkbox';
        $this->Field->attributes->value = 5;

        $this->assertEquals('5', $this->Field->getFormattedValue());
    }



    public function test_field_toJson_returns_a_valid_json_string()
    {
        $json = $this->Field->toJson();

        $this->assertJson($json);
    }

    public function test_field_converting_to_them_from_json_returns_a_matching_object()
    {
        $json = $this->Field->toJson();

        $newField = new Field('different_name');
        $newField->fromJson($json);

        $this->assertEquals($this->Field, $newField);
    }

    public function test_field_converting_to_from_json_when_is_sub_form_works()
    {
        $json = $this->Field->toJson();

        $newField = new Field('my_field');
        $newField->fromJson($json);

        $this->assertEquals($this->Field, $newField);
    }

    public function test_field_can_make_a_view_without_breaking()
    {
        $view = $this->Field->MakeView();

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
    }

}
