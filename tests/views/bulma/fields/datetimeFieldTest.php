<?php namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\datetimeFieldTestInterface;

class datetimeFieldTest extends FieldViewBulmaTestCase implements datetimeFieldTestInterface
{
    protected $test_value = '2016-05-04 15:25:00';
    protected $test_type = 'datetime-local';

    // Run all basic tests

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = $this->test_value;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // & for good measure
        $this->assertEquals('2016-05-04T15:25', $input->value);
    }


}
