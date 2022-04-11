<?php

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\dateFieldTestInterface;

class dateFieldTest extends FieldViewTestCase implements dateFieldTestInterface
{
    protected $test_value = '2016-05-04';
    protected $test_type = 'date';

    // Run all basic tests
}
