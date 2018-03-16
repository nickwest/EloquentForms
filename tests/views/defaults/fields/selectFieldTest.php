<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\selectFieldTestInterface;

class selectFieldTest extends FieldViewTestCase implements selectFieldTestInterface
{
    protected $test_value = '2';
    protected $test_type = 'select';
    protected $test_options = [1 => 'Yes', 2 => 'No'];
    protected $test_tag = 'select';

    // Run all basic tests

    public function test_field_has_all_possible_options()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $options = $dom->find('option');

        $actual_options = [];
        foreach($options as $option){
            $actual_options[$option->value] = trim($option->innertext);
        }

        $this->assertEquals($this->test_options, $actual_options);
    }

    public function test_field_can_have_multiple_attribute_set()
    {
        $this->Field->attributes->multi_key = true;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $select = current($dom->find('select'));

        $this->assertSame(true, $select->multiple);
    }

    public function test_field_has_proper_option_selected_when_value_is_set()
    {
        $this->Field->attributes->value = 1;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $options = $dom->find('option');

        $yes_option = array_shift($options);
        $no_option = array_shift($options);

        $this->assertSame(true, $yes_option->selected);
        $this->assertSame(false, $no_option->selected);
    }

    public function test_field_can_have_multiple_values()
    {
        $this->Field->attributes->value = [1, 2];

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $options = $dom->find('option');

        $yes_option = array_shift($options);
        $no_option = array_shift($options);

        // Checkbox has a value even when not selected
        $this->assertSame('1', $yes_option->value);
        $this->assertSame('2', $no_option->value);

        // Both are checked
        $this->assertSame(true, $yes_option->selected);
        $this->assertSame(true, $no_option->selected);
    }

    public function test_field_has_correct_value_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // select tags do not have value attributes
        $this->assertSame(false, $input->value);
    }

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = $this->test_value;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));
        $options = $dom->find('option');

        $yes_option = array_shift($options);
        $no_option = array_shift($options);

        // select tags do not have value attributes
        $this->assertSame(false, $input->value);

        // Both are checked
        $this->assertSame(false, $yes_option->selected);
        $this->assertSame(true, $no_option->selected);
    }

}
