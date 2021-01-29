<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\numberFieldTestInterface;

class numberFieldTest extends FieldViewBulmaTestCase implements numberFieldTestInterface
{
    protected $test_value = '525600';
    protected $test_type = 'number';

    // Run all basic tests
}
