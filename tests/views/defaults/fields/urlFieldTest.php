<?php namespace Nickwest\EloquentForms\test\views\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\urlFieldTestInterface;

class urlFieldTest extends FieldViewTestCase implements urlFieldTestInterface
{
    protected $test_value = 'https://www.google.com';
    protected $test_type = 'url';

    // Run all basic tests
}
