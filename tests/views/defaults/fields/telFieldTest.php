<?php

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\telFieldTestInterface;

class telFieldTest extends FieldViewTestCase implements telFieldTestInterface
{
    protected $test_value = '(206) 555-1212 x505';
    protected $test_type = 'tel';

    // Run all basic tests

    // TODO: add tel validation tests?
}
