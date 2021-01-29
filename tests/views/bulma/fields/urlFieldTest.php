<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\urlFieldTestInterface;

class urlFieldTest extends FieldViewBulmaTestCase implements urlFieldTestInterface
{
    protected $test_value = 'https://www.google.com';
    protected $test_type = 'url';

    // Run all basic tests
}
