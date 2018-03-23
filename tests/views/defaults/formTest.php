<?php namespace Nickwest\EloquentForms\Test\view\defaults;

use Faker;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Test\Sample;
use Nickwest\EloquentForms\Test\TestCase;

class formTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations/'));

        // Sample is a class declared in the bottom of this file
        // It is only used in these tests
        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    public function test_form_view_has_a_form()
    {
        $dom = HtmlDomParser::str_get_html($this->Model->getFormView([])->render());
        $form = current($dom->find('form'));

        $this->assertNotFalse($form);
    }

    public function test_view_has_all_the_fields()
    {
        $dom = HtmlDomParser::str_get_html($this->Model->getFormView([])->render());
        $fields = $dom->find('div[class="field"]');

        $this->assertEquals(count($this->Model->Form()->getDisplayFields()), count($fields)-1); // -1 for submit buttons
    }

    public function test_form_has_enctype_attribute_when_it_has_a_file_field()
    {
        $dom = HtmlDomParser::str_get_html($this->Model->getFormView([])->render());
        $form = current($dom->find('form'));

        $this->assertEquals('multipart/form-data', $form->enctype);
    }

    public function test_form_has_submit_buttons()
    {
        $dom = HtmlDomParser::str_get_html($this->Model->getFormView([])->render());
        $buttons = current($dom->find('div[class="submit-buttons"]'));

        $this->assertNotFalse($buttons);
    }

    // Don't need to test individual field views because Field tests do that.

    // Don't need to test Submit button details, because submit button tests do that.
}
