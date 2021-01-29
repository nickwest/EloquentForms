<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\emailFieldTestInterface;

class emailFieldTest extends FieldViewBulmaTestCase implements emailFieldTestInterface
{
    protected $test_value = 'test@example.org';
    protected $test_type = 'email';

    // Run all basic tests
}
