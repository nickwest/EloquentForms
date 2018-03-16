<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\emailFieldTestInterface;

class emailFieldTest extends FieldViewTestCase implements emailFieldTestInterface
{
    protected $test_value = 'test@example.org';
    protected $test_type = 'email';

    // Run all basic tests
}
