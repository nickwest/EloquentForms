<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Illuminate\Validation\Rule;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;
use Nickwest\EloquentForms\Exceptions\InvalidCustomFieldObjectException;

use Nickwest\EloquentForms\Test\TestCase;

class HasFieldTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->fields = [];
        $this->sub_fields = [];
        $this->validation_rules = [];

        $this->Faker = Faker\Factory::create();

        $this->Form = $this->createComplexForm();
    }


}
