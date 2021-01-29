<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use KubAT\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\passwordFieldTestInterface;

class passwordFieldTest extends FieldViewBulmaTestCase implements passwordFieldTestInterface
{
    protected $test_value = 'this_is_a_bad_password_probably';
    protected $test_type = 'password';

    // Run all basic tests

    // Override some tests since Password behaves a little differently

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = $this->test_value;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        // A password field should never have a value even if one is set.
        $this->assertSame(true, $input->value);
    }
}
