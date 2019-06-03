<?php namespace Nickwest\EloquentForms\Test\view\bulma;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Form;

use Nickwest\EloquentForms\Test\TestCase;

class submitButtonsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->Form = new Form();
        $this->Form->addField('the_only_field');
        $this->Form->the_only_field = 'So lonely';
    }

    public function test_form_view_has_submit_button_container()
    {
        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));

        $this->assertNotFalse($submit_container);
    }

    public function test_form_view_has_default_submit_button()
    {
        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));
        $submit_buttons = $submit_container->find('button');

        $this->assertEquals(1, count($submit_buttons));

        $submit_button = current($submit_buttons);
        $this->assertEquals('submit_button', $submit_button->name);
        $this->assertEquals('Submit', $submit_button->value);
        $this->assertEquals('submit', $submit_button->type);
        $this->assertEquals('input-submit_button', $submit_button->id);
        $this->assertEquals('button', $submit_button->class);
    }

    public function test_form_view_can_have_multiple_submit_buttons()
    {
        $this->Form->addSubmitButton('submit_delete', 'Delete', null, 'is-danger');

        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));
        $submit_buttons = $submit_container->find('button');

        $this->assertEquals(2, count($submit_buttons));

        $submit_button = end($submit_buttons);
        $this->assertEquals('submit_delete', $submit_button->name);
        $this->assertEquals('Delete', $submit_button->value);
        $this->assertEquals('submit', $submit_button->type);
        $this->assertEquals('input-submit_delete', $submit_button->id);
        $this->assertEquals('button is-danger', $submit_button->class);
    }

    public function test_form_view_can_have_submit_buttons_removed()
    {
        $this->Form->addSubmitButton('submit_delete', 'Delete', null, 'is-danger');
        // Remove the default button
        $this->Form->removeSubmitButton('submit_button', 'Submit');

        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));
        $submit_buttons = $submit_container->find('button');

        $this->assertEquals(1, count($submit_buttons));

        $submit_button = current($submit_buttons);
        $this->assertEquals('submit_delete', $submit_button->name);
        $this->assertEquals('Delete', $submit_button->value);
        $this->assertEquals('submit', $submit_button->type);
        $this->assertEquals('input-submit_delete', $submit_button->id);
        $this->assertEquals('button is-danger', $submit_button->class);
    }

    public function test_form_view_can_have_submit_buttons_edited()
    {
        $Button = $this->Form->getSubmitButton('submit_button', 'Submit');
        $Button->attributes->addClass('super-duper');
        $Button->attributes->value = 'Super';

        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));
        $submit_buttons = $submit_container->find('button');
        $submit_button = current($submit_buttons);

        $this->assertEquals('submit_button', $submit_button->name);
        $this->assertEquals('Super', $submit_button->value);
        $this->assertEquals('submit', $submit_button->type);
        $this->assertEquals('input-submit_button', $submit_button->id);
        $this->assertEquals('button super-duper', $submit_button->class);
    }

    public function test_form_view_can_have_submit_buttons_renamed()
    {
        $this->Form->renameSubmitButton('submit_button', 'Submit', 'save_button', 'Save');

        $dom = HtmlDomParser::str_get_html($this->Form->makeView()->render());
        $submit_container = current($dom->find('div[class=submit-buttons]'));
        $submit_buttons = $submit_container->find('button');
        $submit_button = current($submit_buttons);

        $this->assertEquals('save_button', $submit_button->name);
        $this->assertEquals('Save', $submit_button->value);
        $this->assertEquals('submit', $submit_button->type);
        $this->assertEquals('input-save_button', $submit_button->id);
        $this->assertEquals('button', $submit_button->class);
    }

}
