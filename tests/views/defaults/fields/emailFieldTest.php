<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\emailFieldTestInterface;

class emailFieldTest extends FieldViewTestCase implements emailFieldTestInterface
{
    protected $test_value = 'test@example.org';
    protected $test_type = 'email';

    // Run all basic tests
}
