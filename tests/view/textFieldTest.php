<?php namespace Nickwest\EloquentForms\Test\view;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;

use Nickwest\EloquentForms\Test\TestCase;

class textFieldTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->Field = new Field('my_test_field');
    }

    // Attributes

    public function test_field_has_correct_type_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('text', $input->type);
    }

    public function test_field_has_correct_name_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('my_test_field', $input->name);
    }

    public function test_field_has_correct_name_attribute_when_changed()
    {
        $this->Field->attributes->name = 'first_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('first_name', $input->name);
    }

    public function test_field_has_correct_id_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('input-my_test_field', $input->id);
    }

    public function test_field_has_correct_id_attribute_when_changed()
    {
        $this->Field->attributes->id = 'first_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('input-first_name', $input->id);
    }

    public function test_field_has_correct_id_attribute_when_prefix_changed()
    {
        $this->Field->attributes->id_prefix = 'myprefix_';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('myprefix_my_test_field', $input->id);
    }

    public function test_field_has_correct_class_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEmpty($input->class);
    }

    public function test_field_has_correct_class_attribute_when_one_class_added()
    {
        $this->Field->attributes->addClass('my-class');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('my-class', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_many_classes_added()
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('my-class two three', trim($input->class));
    }

    public function test_field_has_correct_class_attribute_when_classes_removed()
    {
        $this->Field->attributes->addClass('my-class');
        $this->Field->attributes->addClass('two');
        $this->Field->attributes->addClass('three');
        $this->Field->attributes->removeClass('two');
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('my-class three', trim($input->class));
    }

    public function test_field_has_correct_value_attribute()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        // Blank by deafult (no value set)
        $this->assertSame(true, $input->value);
    }

    public function test_field_has_correct_value_attribute_when_changed()
    {
        $this->Field->attributes->value = 'My string value 123&';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        // & for good measure
        $this->assertEquals('My string value 123&', $input->value);
    }

    public function test_field_can_have_valueless_attributes()
    {
        $this->Field->attributes->required = null;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertSame(true, $input->required);
    }

    public function test_field_can_have_data_attributes()
    {
        $this->Field->attributes->{'data-mydata'} = 42;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('42', $input->{'data-mydata'});
    }

    public function test_field_can_have_data_attributes_with_json_values()
    {
        $this->Field->attributes->{'data-mydata'} = '{\'testing\':42}';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('{\'testing\':42}', $input->{'data-mydata'});
    }

    public function test_field_can_have_invalid_attributes()
    {
        $this->Field->attributes->unreal_attr = 'Unreal!';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('Unreal!', $input->unreal_attr);
    }

    public function test_field_can_have_vue_js_style_attributes()
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
        $input = current($dom->find('input'));

        $this->assertEquals('[activeClass, errorClass]', $input->{'v-bind:class'});
        $this->assertEquals("{ display: ['-webkit-box', '-ms-flexbox', 'flex'] }", $input->{'v-bind:style'});
        $this->assertEquals('(item, index) in items', $input->{'v-for'});
        $this->assertEquals('item.id', $input->{':key'});
        $this->assertEquals('age', $input->{'v-model.number'});
        $this->assertEquals('show = !show', $input->{'@click'});
        $this->assertSame(true, $input->{'v-else'});
    }


    // Labels

    public function test_field_has_proper_label()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('input-my_test_field', $label->for);
        // Need to trim whitespace from plaintext since the view will likely have it.
        $this->assertEquals('My test field', trim($label->plaintext));
    }

    public function test_field_has_proper_label_when_attributes_changed()
    {
        $this->Field->attributes->id = 'new_id';
        $this->Field->attributes->name = 'new_name';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('input-new_id', $label->for);

        // Changing the name attribute won't change the label
        $this->assertEquals('My test field', trim($label->plaintext));
    }

    public function test_field_has_proper_label_when_label_changed()
    {
        $this->Field->label = 'Awesome new Label';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('Awesome new Label', trim($label->plaintext));
    }

    public function test_field_has_proper_label_suffix_when_set()
    {
        $this->Field->label_suffix = ':';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $label = current($dom->find('label'));

        $this->assertEquals('My test field:', trim($label->plaintext));

    }


    // Properietary output stuff

    public function test_field_has_multi_key_when_set()
    {
        $this->Field->attributes->multi_key = 4;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $input = current($dom->find('input'));

        $this->assertEquals('my_test_field[4]', trim($input->name));
    }

    public function test_field_has_example_when_set()
    {
        $this->Field->example = 'This is an <strong>awesome</strong> example';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $example = current($dom->find('p'));

        $this->assertEquals('example', $example->class);
        $this->assertEquals('This is an <strong>awesome</strong> example', trim($example->innertext));
    }

    public function test_field_has_note_when_set()
    {
        $this->Field->note = 'Something <a href="https://google.com">to give</a> more info';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $note = current($dom->find('p'));

        $this->assertEquals('note', $note->class);
        $this->assertEquals('Something <a href="https://google.com">to give</a> more info', trim($note->innertext));
    }

    public function test_field_has_error_message_when_set()
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

}
