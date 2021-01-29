<?php namespace Nickwest\EloquentForms\test\views\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\monthFieldTestInterface;

class monthFieldTest extends FieldViewTestCase implements monthFieldTestInterface
{
    protected $test_value = '2016-05';
    protected $test_type = 'month';

    // Run all basic tests
}
