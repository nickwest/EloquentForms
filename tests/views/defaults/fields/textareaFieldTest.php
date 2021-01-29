<?php namespace Nickwest\EloquentForms\test\views\defaults\fields;

use KubAT\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\textareaFieldTestInterface;

class textareaFieldTest extends FieldViewTestCase implements textareaFieldTestInterface
{
    protected $test_value = 'Donut <strong>jelly-o</strong> wafer sugar plum marzipan toffee cheesecake topping. Muffin chocolate donut. Cake wafer sugar plum. Cookie halvah powder gingerbread oat cake muffin. Marshmallow chocolate bar candy cheesecake bear claw tiramisu sweet tootsie roll. Bonbon sesame snaps donut gummies cookie marshmallow pie. Bonbon jujubes toffee. Toffee muffin cotton candy gingerbread cotton candy jelly-o lollipop. Cookie chocolate sugar plum jelly powder pastry cheesecake. Candy canes tart powder pudding cookie marshmallow gummies bonbon topping. Dessert jelly-o gummi bears biscuit liquorice tootsie roll. Lollipop chocolate cake muffin toffee gingerbread. Bonbon icing jujubes gingerbread chocolate bar. Pie gummi bears pastry.';
    protected $test_type = 'textarea';
    protected $test_tag = 'textarea';

    // Run all basic tests

    public function test_field_has_correct_type_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertFalse($input->type);
    }

    public function test_field_has_correct_value_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // textarea shouldn't have a value attribute at all
        $this->assertSame(false, $input->value);

        // inner text should be empty, no value set
        $this->assertEquals('', trim($input->innertext));
    }

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = $this->test_value;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // textarea shouldn't have a value attribute at all
        $this->assertSame(false, $input->value);

        // But the value between the tags should be equal to the value
        $this->assertEquals(htmlspecialchars($this->test_value), trim($input->innertext));
    }


}
