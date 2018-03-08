<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Test\TestCase;

class FormTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mock = $this->getMockForTrait('Nickwest\EloquentForms\FormTrait');
    }

    public function test_formtrait_form_returns_a_form_object()
    {
        $this->assertInstanceOf(\Nickwest\EloquentForms\Form::class, $this->mock->Form());
    }


}
