<?php namespace Nickwest\EloquentForms\Test\views\bulma;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Field;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;

class selectFieldTest extends FieldViewBulmaTestCase
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
}
