<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Field;
use Sunra\PhpSimple\HtmlDomParser;
use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\radioFieldTestInterface;

class radioFieldTest extends FieldViewBulmaTestCase implements radioFieldTestInterface
{
    protected $test_value = 'no';
    protected $test_type = 'radio';
    protected $test_options = ['yes' => 'Yes', 'no' => 'No'];
    protected $expected_type_class = '';

    // Run all basic tests

    // Override some tests

    public function test_field_has_correct_value_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertSame('yes', $yes_input->value);
        $this->assertSame('no', $no_input->value);
    }

    public function test_field_has_proper_label(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');
        $group_label = array_shift($labels);
        $yes_option_label = array_shift($labels);
        $no_option_label = array_shift($labels);

        $this->assertEquals('My test field', trim($group_label->plaintext));

        $this->assertEquals('input-my_test_field-yes', $yes_option_label->for);
        $this->assertEquals('Yes', trim($yes_option_label->plaintext));

        $this->assertEquals('input-my_test_field-no', $no_option_label->for);
        $this->assertEquals('No', trim($no_option_label->plaintext));
    }

    public function test_field_has_proper_label_when_attributes_changed(): void
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
        $this->assertEquals('input-new_id-yes', $yes_option_label->for);
        $this->assertEquals('Yes', trim($yes_option_label->plaintext));

        $this->assertEquals('input-new_id-no', $no_option_label->for);
        $this->assertEquals('No', trim($no_option_label->plaintext));
    }

    // Add "selected" tests

    public function test_field_has_selected_attribute_when_value_is_equal(): void
    {
        $this->Field->attributes->value = 'yes';

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertSame(true, $yes_input->checked);
        $this->assertSame(false, $no_input->checked);
    }

    public function test_field_has_correct_id_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('input-my_test_field-yes', $yes_input->id);
        $this->assertEquals('input-my_test_field-no', $no_input->id);
    }

    public function test_field_has_correct_id_attribute_when_changed(): void
    {
        $this->Field->attributes->id = 'new_id';

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('input-new_id-yes', $yes_input->id);
        $this->assertEquals('input-new_id-no', $no_input->id);
    }

    public function test_field_has_correct_id_attribute_when_prefix_changed(): void
    {
        $this->Field->attributes->id_prefix = 'myprefix_';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('myprefix_my_test_field-yes', $yes_input->id);
        $this->assertEquals('myprefix_my_test_field-no', $no_input->id);
    }

    public function test_field_has_correct_value_attribute_when_changed(): void
    {
        $this->Field->attributes->value = 'no';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find($this->test_tag);
        $yes_input = array_shift($inputs);
        $no_input = array_shift($inputs);

        $this->assertEquals('yes', $yes_input->value);
        $this->assertEquals('no', $no_input->value);

        $this->assertSame(false, $yes_input->checked);
        $this->assertSame(true, $no_input->checked);
    }
}
