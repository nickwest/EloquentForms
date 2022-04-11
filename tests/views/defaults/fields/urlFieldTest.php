<?php

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\urlFieldTestInterface;

class urlFieldTest extends FieldViewTestCase implements urlFieldTestInterface
{
    protected $test_value = 'https://www.google.com';
    protected $test_type = 'url';

    // Run all basic tests
}
