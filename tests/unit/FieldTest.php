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
    }

    public function test_field_has_some_stuff_set_on_construct()
    {
        $Field = new Field('my_field');

        $this->assertEquals('my_field', $Field->Attributes->name);
        $this->assertEquals('text', $Field->Attributes->type);
        $this->assertEquals('my_field', $Field->Attributes->id);

        $this->assertEquals('my_field', $Field->getOriginalName());
        $this->assertEquals('my_field', $Field->getOriginalId());
        $this->assertEquals('My field', $Field->label);
    }

    public function test_field_getViewNamespace_returns_correct_name()
    {
        $Field = new Field('my_field');

        // When it's a default theme
        $this->assertEquals('Nickwest\\EloquentForms', $Field->getViewNamespace());

        // When we apply Bulma
        $Field->Theme = new \Nickwest\EloquentForms\bulma\Theme();
        $this->assertEquals('Nickwest\\EloquentForms', $Field->getViewNamespace());
    }

    public function test_field_isSubform_returns_if_field_is_a_subform()
    {
        $Field = new Field('my_subform');

        $Form = new Form();
        $Form->addFields(['sub1', 'sub2']);

        $Field->Subform = $Form;

        $this->assertTrue($Field->isSubform());
    }

    public function test_field_getTemplate_returns_the_correct_template_for_fields()
    {
        $Field = new Field('my_field');

        $Field->Attributes->type = 'textarea';
        $this->assertEquals('Nickwest\EloquentForms::fields.textarea', $Field->getTemplate());

        $Field->Attributes->type = 'select';
        $this->assertEquals('Nickwest\EloquentForms::fields.select', $Field->getTemplate());

        // And with a different theme
        $Field->Theme = new \Nickwest\EloquentForms\bulma\Theme();
        $this->assertEquals('Nickwest\EloquentForms::fields.select', $Field->getTemplate());
    }

    public function test_field_setOption_will_set_a_sign_option_to_the_field()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'one'];
        $Field->setOption(1, 'one');

        $this->assertEquals($test_options, $Field->getOptions());
    }

    public function test_field_setOptions_will_set_options_to_a_field()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'different', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $options = $Field->getOptions();

        $this->assertEquals($test_options, $options);
    }

    public function test_field_setOptions_overwrites_previous_set_values()
    {
        $Field = new Field('my_field');

        $Field->setOption(1, 'Wrong');
        $Field->setOption(2, 'Option');

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $this->assertEquals($test_options, $Field->getOptions());
    }

    public function test_field_setOptions_throws_an_exception_when_option_values_are_not_strings()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'different', 2 => 'two', 44 => ['Fourtyfour']];

        $this->expectException(OptionValueException::class);
        $Field->setOptions($test_options);
    }

    public function test_field_removeOption_will_remove_an_option()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'different', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $Field->removeOption(2);

        unset($test_options[2]);
        $this->assertEquals($test_options, $Field->getOptions());
    }

    public function test_field_setDisabledOptions_sets_options_to_be_disabled()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $Field->setDisabledOptions([1,44]);

        $this->assertAttributeEquals([1,44], 'disabled_options', $Field);
    }

    public function test_field_setDisalbedOptions_throws_an_exception_if_invalid_options_are_passed()
    {
        $Field = new Field('my_field');

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $this->expectException(OptionValueException::class);
        $Field->setDisabledOptions([1,44,4]);
    }

    public function test_field_toJson_returns_a_valid_json_string()
    {
        $Field = new Field('my_field');

        $Field->CustomField = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField;

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $Field->setDisabledOptions([1,44]);

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
        $Field->options_container_class = 'options';
        $Field->input_wrapper_class = 'Snoop-Dogg';
        $Field->option_wrapper_class = 'Macklemore';
        $Field->option_label_class = 'so_many_classes';

        $json = $Field->toJson();

        $this->assertJson($json);
    }

    public function test_field_converting_to_them_from_json_returns_a_matching_object()
    {
        $Field = new Field('my_field');

        $Field->CustomField = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField;

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $Field->setDisabledOptions([1,44]);

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
        $Field->options_container_class = 'options';
        $Field->input_wrapper_class = 'Snoop-Dogg';
        $Field->option_wrapper_class = 'Macklemore';
        $Field->option_label_class = 'so_many_classes';

        $json = $Field->toJson();

        $newField = new Field('different_name');
        $newField->fromJson($json);

        $this->assertEquals($Field, $newField);
    }

    public function test_field_converting_to_from_json_when_is_sub_form_works()
    {
        $Field = new Field('my_field');

        $Form = new Form();
        $Form->addFields(['sub1', 'sub2']);

        $Field->Subform = $Form;
        $json = $Field->toJson();

        $newField = new Field('my_field');
        $newField->fromJson($json);

        $this->assertEquals($Field, $newField);
    }

    public function test_field_can_make_a_view_without_breaking()
    {
        $Field = new Field('my_field');

        $Field->CustomField = new \Nickwest\EloquentForms\CustomFields\daysofweek\CustomField;

        $test_options = [1 => 'one', 2 => 'two', 44 => 'Fourtyfour'];
        $Field->setOptions($test_options);

        $Field->setDisabledOptions([1,44]);

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
        $Field->options_container_class = 'options';
        $Field->input_wrapper_class = 'Snoop-Dogg';
        $Field->option_wrapper_class = 'Macklemore';
        $Field->option_label_class = 'so_many_classes';

        $view = $Field->MakeView();

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
    }


}
