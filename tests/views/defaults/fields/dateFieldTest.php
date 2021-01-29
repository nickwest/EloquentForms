<?php namespace Nickwest\EloquentForms\test\view\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\dateFieldTestInterface;

class dateFieldTest extends FieldViewTestCase implements dateFieldTestInterface
{
    protected $test_value = '2016-05-04';
    protected $test_type = 'date';

    // Run all basic tests
}
