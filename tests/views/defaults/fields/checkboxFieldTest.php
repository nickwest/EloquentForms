<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Field;
use Sunra\PhpSimple\HtmlDomParser;
use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\checkboxFieldTestInterface;

class checkboxFieldTest extends FieldViewTestCase implements checkboxFieldTestInterface
{
    protected $test_value = '1';
    protected $test_type = 'checkbox';
    protected $test_id_suffix = '-1';
    protected $test_options = [1 => 'Yes'];

    // Run all basic tests

    // Override some tests

    public function test_field_has_correct_value_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // Checkbox has a value even when not selected
        $this->assertSame('1', $input->value);
    }

    public function test_field_has_proper_label(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');
        $group_label = array_shift($labels);
        $option_label = array_shift($labels);

        $this->assertEquals('My test field', trim($group_label->plaintext));

        $this->assertEquals('input-my_test_field-1', $option_label->for);
        $this->assertEquals('Yes', trim($option_label->plaintext));
    }

    public function test_field_has_proper_label_when_attributes_changed(): void
    {
        $this->Field->attributes->id = 'new_id';
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');
        $group_label = array_shift($labels);
        $option_label = array_shift($labels);

        // This stays the same
        $this->assertEquals('My test field', trim($group_label->plaintext));

        // Should have a new ID, but the same option label
        $this->assertEquals('input-new_id-1', $option_label->for);
        $this->assertEquals('Yes', trim($option_label->plaintext));
    }

    // Add "selected" tests

    public function test_field_has_selected_attribute_when_value_is_equal(): void
    {
        $this->Field->attributes->value = 1;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertSame(true, $input->checked);
    }
}
