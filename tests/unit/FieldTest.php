<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;

use Nickwest\EloquentForms\Test\TestCase;

class FieldTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_field_has_some_stuff_set_on_construct()
    {
        $Field = new Field('my_field');

        $this->assertEquals('my_field', $Field->Attributes->name);
        $this->assertEquals('text', $Field->Attributes->type);
        $this->assertEquals('input-my_field', $Field->Attributes->id);

        $this->assertEquals('my_field', $Field->getOriginalName());
        $this->assertEquals('input-my_field', $Field->getOriginalId());
        $this->assertEquals('My field', $Field->label);
    }

    public function test_field_getViewNamespace_returns_correct_name()
    {
        $Field = new Field('my_field');

        // When it's a default theme
        $this->assertEquals('Nickwest\\EloquentForms', $Field->getViewNamespace());

        // When we apply Bulma
        $Field->Theme = new \Nickwest\EloquentForms\bulma\Theme();
        $this->assertEquals('Nickwest\\EloquentForms', $Field->getViewNamespace());
    }

    public function test_field_isSubform_returns_if_field_is_a_subform()
    {
        $Field = new Field('my_subform');

        $Form = new Form();
        $Form->addFields(['sub1', 'sub2']);

        $Field->Subform = $Form;

        $this->assertTrue($Field->isSubform());
    }

    public function test_field_getTemplate_returns_the_correct_template_for_fields()
    {
        $Field = new Field('my_field');

        $Field->Attributes->type = 'textarea';

        $this->assertEquals('Nickwest\EloquentForms::fields.textarea', $Field->getTemplate());


    }

}
