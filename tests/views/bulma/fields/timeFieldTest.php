<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\timeFieldTestInterface;

class timeFieldTest extends FieldViewBulmaTestCase implements timeFieldTestInterface
{
    protected $test_value = '15:44';
    protected $test_type = 'time';

    // Run all basic tests
}
