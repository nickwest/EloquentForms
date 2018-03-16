<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\hiddenFieldTestInterface;

class hiddenFieldTest extends FieldViewTestCase implements hiddenFieldTestInterface
{
    protected $test_value = 'my_value';
    protected $test_type = 'hidden';

    // Run all basic tests

    // Override some basic tests because Hidden is different

    public function test_field_has_proper_label()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        // Hidden fields will never show a label
        $this->assertSame(false, $label);
    }

    public function test_field_has_proper_label_when_attributes_changed()
    {
        $this->Field->attributes->id = 'new_id';
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        // Hidden fields should still never show a label
        $this->assertSame(false, $label);
    }

    public function test_field_has_proper_label_when_label_changed()
    {
        $this->Field->label = 'Awesome new Label';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        // Hidden fields should still never show a label
        $this->assertSame(false, $label);
    }

    public function test_field_has_proper_label_suffix_when_set()
    {
        $this->Field->label_suffix = ':';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        // Hidden fields should still never show a label
        $this->assertSame(false, $label);
    }

    public function test_field_has_example_when_set()
    {
        $this->Field->example = 'This is an <strong>awesome</strong> example';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $example = current($dom->find('p'));

        // Hidden fields will never show an example
        $this->assertSame(false, $example);
    }

    public function test_field_has_note_when_set()
    {
        $this->Field->note = 'Something <a href="https://google.com">to give</a> more info';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $note = current($dom->find('p'));

        // Hidden fields will never show a note
        $this->assertSame(false, $note);
    }

    public function test_field_has_a_container_div()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $div = current($dom->find('div'));

        // Hidden fields don't have a container, so make sure there isn't one.
        $this->assertSame(false, $div);
    }

    public function test_field_container_div_has_valid_attributes()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $div = current($dom->find('div'));

        // Hidden fields don't have a container, so we don't care about its attributes, just pass
        $this->assertSame(false, $div);
    }

}
