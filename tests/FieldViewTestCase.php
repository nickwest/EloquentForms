<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test;

/*
 * FieldViewTestCase includes basic field tests
 * Each Field view uses different templates and ruins different logic
 * It's necessary to test each field type for completely coverage
 * Some fields will have differences that will require test overrides
 *
 * NOTE: These fields do not extend this TestCase class:
 *    contentblock, datalist,
 */

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;
use Sunra\PhpSimple\HtmlDomParser;

abstract class FieldViewTestCase extends TestCase
{
    protected $test_value = 'My test string & cool stuff!';
    protected $test_type = 'text';
    protected $test_tag = 'input';
    protected $test_id_suffix = '';
    protected $test_options = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->Field = new Field('my_test_field');

        $this->Field->attributes->type = $this->test_type;

        if (is_array($this->test_options)) {
            $this->Field->options->setOptions($this->test_options);
        }
    }

    // Attributes

    public function test_field_has_correct_type_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals($this->test_type, $input->type);
    }

    public function test_field_has_correct_name_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $expected = 'my_test_field';
        if ($this->Field->attributes->type === 'checkbox' && count($this->Field->options->getOptions()) > 1) {
            $expected .= '[]';
        }

        $this->assertEquals($expected, $input->name);
    }

    public function test_field_has_correct_name_attribute_when_changed(): void
    {
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $expected = 'new_name';
        if ($this->Field->attributes->type === 'checkbox' && count($this->Field->options->getOptions()) > 1) {
            $expected .= '[]';
        }

        $this->assertEquals($expected, $input->name);
    }

    public function test_field_has_correct_id_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('input-my_test_field'.$this->test_id_suffix, $input->id);
    }

    public function test_field_has_correct_id_attribute_when_changed(): void
    {
        $this->Field->attributes->id = 'new_id';

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('input-new_id'.$this->test_id_suffix, $input->id);
    }

    public function test_field_has_correct_id_attribute_when_prefix_changed(): void
    {
        $this->Field->attributes->id_prefix = 'myprefix_';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('myprefix_my_test_field'.$this->test_id_suffix, $input->id);
    }

    public function test_field_has_correct_class_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEmpty($input->class);
    }

    public function test_field_has_correct_class_attribute_when_one_class_added(): void
    {
        $this->Field->attributes->addClass('my-class');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('my-class', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_many_classes_added(): void
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('my-class two three', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_classes_removed(): void
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $this->Field->attributes->removeClass('two');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('my-class three', trim($input->class));
    }

    public function test_field_has_correct_value_attribute(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // Blank by deafult (no value set)
        $this->assertSame(true, $input->value);
    }

    public function test_field_has_correct_value_attribute_when_changed(): void
    {
        $this->Field->attributes->value = $this->test_value;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        // & for good measure
        $this->assertEquals($this->test_value, $input->value);
    }

    public function test_field_can_have_valueless_attributes(): void
    {
        $this->Field->attributes->required = null;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertSame(true, $input->required);
    }

    public function test_field_can_have_data_attributes(): void
    {
        $this->Field->attributes->{'data-mydata'} = 42;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('42', $input->{'data-mydata'});
    }

    public function test_field_can_have_data_attributes_with_json_values(): void
    {
        $this->Field->attributes->{'data-mydata'} = '{\'testing\':42}';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('{\'testing\':42}', $input->{'data-mydata'});
    }

    public function test_field_can_have_invalid_attributes(): void
    {
        $this->Field->attributes->unreal_attr = 'Unreal!';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('Unreal!', $input->unreal_attr);
    }

    public function test_field_can_have_vue_js_style_attributes(): void
    {
        // Lots of funky types of attributes to support VueJS

        // v-bind:class="[activeClass, errorClass]"
        $this->Field->attributes->{'v-bind:class'} = '[activeClass, errorClass]';
        // v-bind:style="{ display: ['-webkit-box', '-ms-flexbox', 'flex'] }"
        $this->Field->attributes->{'v-bind:style'} = "{ display: ['-webkit-box', '-ms-flexbox', 'flex'] }";
        // v-for="(item, index) in items"
        $this->Field->attributes->{'v-for'} = '(item, index) in items';
        // :key
        $this->Field->attributes->{':key'} = 'item.id';
        // v-model.number="age"
        $this->Field->attributes->{'v-model.number'} = 'age';
        // @click="show = !show"
        $this->Field->attributes->{'@click'} = 'show = !show';
        // v-else
        $this->Field->attributes->{'v-else'} = null;

        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('[activeClass, errorClass]', $input->{'v-bind:class'});
        $this->assertEquals("{ display: ['-webkit-box', '-ms-flexbox', 'flex'] }", $input->{'v-bind:style'});
        $this->assertEquals('(item, index) in items', $input->{'v-for'});
        $this->assertEquals('item.id', $input->{':key'});
        $this->assertEquals('age', $input->{'v-model.number'});
        $this->assertEquals('show = !show', $input->{'@click'});
        $this->assertSame(true, $input->{'v-else'});
    }

    // Labels

    public function test_field_has_proper_label(): void
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('input-my_test_field', $label->for);
        // Need to trim whitespace from plaintext since the view will likely have it.
        $this->assertEquals('My test field', trim($label->plaintext));
    }

    public function test_field_has_proper_label_when_attributes_changed(): void
    {
        $this->Field->attributes->id = 'new_id';
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('input-new_id', $label->for);

        // Changing the name attribute won't change the label
        $this->assertEquals('My test field', trim($label->plaintext));
    }

    public function test_field_has_proper_label_when_label_changed(): void
    {
        $this->Field->label = 'Awesome new Label';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('Awesome new Label', trim($label->plaintext));
    }

    public function test_field_has_proper_label_suffix_when_set(): void
    {
        $this->Field->label_suffix = ':';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('My test field:', trim($label->plaintext));
    }

    // Properietary output stuff

    public function test_field_has_multi_key_when_set(): void
    {
        $this->Field->attributes->multi_key = 4;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find($this->test_tag));

        $this->assertEquals('my_test_field[4]', trim($input->name));
    }

    public function test_field_has_example_when_set(): void
    {
        $this->Field->example = 'This is an <strong>awesome</strong> example';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $example = current($dom->find('p'));

        $this->assertEquals('example', $example->class);
        $this->assertEquals('This is an <strong>awesome</strong> example', trim($example->innertext));
    }

    public function test_field_has_note_when_set(): void
    {
        $this->Field->note = 'Something <a href="https://google.com">to give</a> more info';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $note = current($dom->find('p'));

        $this->assertEquals('note', $note->class);
        $this->assertEquals('Something <a href="https://google.com">to give</a> more info', trim($note->innertext));
    }

    public function test_field_has_error_message_when_set(): void
    {
        $Form = new Form();
        $Form->addField('awesome_stuff');
        $Form->awesome_stuff->attributes->required = true;

        $Form->isValid();

        $dom = HtmlDomParser::str_get_html($Form->awesome_stuff->makeView()->render());
        $error = current($dom->find('p'));

        $this->assertNotSame(false, $error);
        $this->assertEquals('The awesome stuff field is required.', $error->plaintext);
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

        $expected = 'field-my_test_field';
        if ($this->Field->attributes->type === 'checkbox' && count($this->Field->options->getOptions()) > 1) {
            $expected .= '_1';
        }

        $this->assertEquals($expected, $div->id);
        // Order is arbitrary, so sort to make sure they're equal even if not ordered the same way
        $expected = ['type-'.$this->test_type, 'field', $this->test_type];
        $actual = explode(' ', $div->class);
        $this->assertEquals(sort($expected), $actual = sort($actual));
    }
}
