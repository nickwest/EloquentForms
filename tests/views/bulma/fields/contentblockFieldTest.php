<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Field;
use Sunra\PhpSimple\HtmlDomParser;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\contentblockFieldTestInterface;

// Extend TestCase since Contentblock isn't a real field
class contentblockFieldTest extends TestCase implements contentblockFieldTestInterface
{
    protected $test_value = 'Donut <strong>jelly-o</strong> wafer sugar plum marzipan toffee cheesecake topping. Muffin chocolate donut. Cake wafer sugar plum. Cookie halvah powder gingerbread oat cake muffin. Marshmallow chocolate bar candy cheesecake bear claw tiramisu sweet tootsie roll. Bonbon sesame snaps donut gummies cookie marshmallow pie. Bonbon jujubes toffee. Toffee muffin cotton candy gingerbread cotton candy jelly-o lollipop. Cookie chocolate sugar plum jelly powder pastry cheesecake. Candy canes tart powder pudding cookie marshmallow gummies bonbon topping. Dessert jelly-o gummi bears biscuit liquorice tootsie roll. Lollipop chocolate cake muffin toffee gingerbread. Bonbon icing jujubes gingerbread chocolate bar. Pie gummi bears pastry.';

    public function setUp(): void
    {
        parent::setUp();

        $this->Field = new Field('my_test_field');

        $this->Field->attributes->type = 'contentblock';
        $this->Field->attributes->value = $this->test_value;
    }

    public function test_field_not_there_if_no_value(): void
    {
        $this->Field->attributes->value = '';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        // Without a value this field shouldn't show up
        $this->assertSame(false, $input);
    }

    public function test_field_has_correct_id_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        // Without a value this field shouldn't show up
        $this->assertEquals('input-my_test_field', $input->id);
    }

    public function test_field_has_correct_id_attribute_when_changed(): void
    {
        $this->Field->attributes->id = 'new_id';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-new_id]'));

        $this->assertEquals('input-new_id', $input->id);
    }

    public function test_field_has_correct_id_attribute_when_prefix_changed(): void
    {
        $this->Field->attributes->id_prefix = 'myprefix_';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=myprefix_my_test_field]'));

        $this->assertEquals('myprefix_my_test_field', $input->id);
    }

    public function test_field_has_correct_class_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        $this->assertEmpty($input->class);
    }

    public function test_field_has_correct_class_attribute_when_one_class_added(): void
    {
        $this->Field->attributes->addClass('my-class');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        $this->assertEquals('my-class', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_many_classes_added(): void
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        $this->assertEquals('my-class two three', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_classes_removed(): void
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $this->Field->attributes->removeClass('two');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        $this->assertEquals('my-class three', trim($input->class));
    }

    public function test_field_has_proper_content(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('div[id=input-my_test_field]'));

        $this->assertEquals($this->test_value, trim($input->innertext));
    }

    // Container

    public function test_field_has_a_container_div(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $div = current($dom->find('div'));

        $this->assertNotSame(false, $div);
    }

    public function test_field_container_div_has_valid_attributes(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $div = current($dom->find('div'));

        $this->assertEquals('field-my_test_field', $div->id);
        // Order is arbitrary, so sort to make sure they're equal even if not ordered the same way
        $expected = ['type-contentblock', 'field', 'contentblock'];
        $actual = explode(' ', $div->class);
        $this->assertEquals(sort($expected), $actual = sort($actual));
    }
}
