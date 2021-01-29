<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\timeFieldTestInterface;

class timeFieldTest extends FieldViewTestCase implements timeFieldTestInterface
{
    protected $test_value = '15:44';
    protected $test_type = 'time';

    // Run all basic tests
}
