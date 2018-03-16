<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\datetimeFieldTestInterface;

class datetimeFieldTest extends FieldViewTestCase implements datetimeFieldTestInterface
{
    protected $test_value = '2016-05-04 15:25:04';
    protected $test_type = 'datetime-local';

    // Run all basic tests
}
