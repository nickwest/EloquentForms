<?php namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\datalistFieldTestInterface;

// Extend TestCase since Contentblock isn't a real field
class datalistFieldTest extends TestCase implements datalistFieldTestInterface
{
    protected $options = [
        1 => 'First',
        'my-key' => 'Something',
        '2016-05-15' => 'A date',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Field = new Field('my_test_field');

        $this->Field->attributes->type = 'datalist';
        $this->Field->options->setOptions($this->options);
    }

    public function test_field_not_there_if_no_options_set()
    {
        $this->Field->options->setOptions([]);
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());

        // Without a options this generates no output at all
        $this->assertSame(false, $dom);
    }

    public function test_field_works_if_options_are_set()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $datalist = current($dom->find('datalist'));

        $this->assertEquals('input-my_test_field', $datalist->id);
    }

    public function test_field_options_in_datalist_are_as_expected()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $datalist = current($dom->find('datalist'));

        $actual = [];
        foreach($datalist->find('option') as $option){
            $actual[$option->value] = $option->label;
        }

        $this->assertEquals($this->options, $actual);
    }

}
