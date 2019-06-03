<?php namespace Nickwest\EloquentForms\Test\views\defaults\fields;

use KubAT\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\checkboxesFieldTestInterface;

class checkboxesFieldTest extends FieldViewTestCase implements checkboxesFieldTestInterface
{
    protected $test_value = '2';
    protected $test_type = 'checkbox';
    protected $test_options = [1 => 'Yes', 2 => 'No'];

    // Run all basic tests


    // Override some tests

    public function test_field_has_correct_id_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('input-my_test_field-1', $yes_input->id);
        $this->assertEquals('input-my_test_field-2', $no_input->id);
    }

    public function test_field_has_correct_id_attribute_when_changed()
    {
        $this->Field->attributes->id = 'new_id';

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('input-new_id-1', $yes_input->id);
        $this->assertEquals('input-new_id-2', $no_input->id);
    }

    public function test_field_has_correct_id_attribute_when_prefix_changed()
    {
        $this->Field->attributes->id_prefix = 'myprefix_';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('myprefix_my_test_field-1', $yes_input->id);
        $this->assertEquals('myprefix_my_test_field-2', $no_input->id);
    }

    public function test_field_has_correct_value_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        // Checkbox has a value even when not selected
        $this->assertSame('1', $yes_input->value);
        $this->assertSame('2', $no_input->value);
    }

    public function test_field_has_proper_label()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');
        $group_label = array_shift($labels);
        $yes_option_label = array_shift($labels);
        $no_option_label = array_shift($labels);

        $this->assertEquals('My test field', trim($group_label->plaintext));

        $this->assertEquals('input-my_test_field-1', $yes_option_label->for);
        $this->assertEquals('Yes', trim($yes_option_label->plaintext));

        $this->assertEquals('input-my_test_field-2', $no_option_label->for);
        $this->assertEquals('No', trim($no_option_label->plaintext));
    }

    public function test_field_has_proper_label_when_attributes_changed()
    {
        $this->Field->attributes->id = 'new_id';
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');
        $group_label = array_shift($labels);
        $yes_option_label = array_shift($labels);
        $no_option_label = array_shift($labels);

        // This stays the same
        $this->assertEquals('My test field', trim($group_label->plaintext));

        // Should have a new ID, but the same option label
        $this->assertEquals('input-new_id-1', $yes_option_label->for);
        $this->assertEquals('Yes', trim($yes_option_label->plaintext));

        $this->assertEquals('input-new_id-2', $no_option_label->for);
        $this->assertEquals('No', trim($no_option_label->plaintext));
    }

    public function test_field_has_selected_attribute_when_value_is_equal()
    {
        $this->Field->attributes->value = $this->test_value;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertSame(false, $yes_input->checked);
        $this->assertSame(true, $no_input->checked);
    }

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = '1';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('1', $yes_input->value);
        $this->assertEquals('2', $no_input->value);

        $this->assertSame(true, $yes_input->checked);
        $this->assertSame(false, $no_input->checked);
    }

    public function test_field_can_have_multiple_values()
    {
        $this->Field->attributes->value = [1, 2];

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        // Checkbox has a value even when not selected
        $this->assertSame('1', $yes_input->value);
        $this->assertSame('2', $no_input->value);

        // Both are checked
        $this->assertSame(true, $yes_input->checked);
        $this->assertSame(true, $no_input->checked);
    }

    public function test_fields_have_brackets_in_name_when_multiple_options_are_set()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertStringEndsWith('[]', $yes_input->name);
        $this->assertStringEndsWith('[]', $no_input->name);
    }



}
