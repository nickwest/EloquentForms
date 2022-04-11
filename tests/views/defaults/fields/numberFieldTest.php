<?php

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\numberFieldTestInterface;

class numberFieldTest extends FieldViewTestCase implements numberFieldTestInterface
{
    protected $test_value = '525600';
    protected $test_type = 'number';

    // Run all basic tests
}
