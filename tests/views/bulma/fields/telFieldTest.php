<?php

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\telFieldTestInterface;

class telFieldTest extends FieldViewBulmaTestCase implements telFieldTestInterface
{
    protected $test_value = '(206) 555-1212 x505';
    protected $test_type = 'tel';

    // Run all basic tests

    // TODO: add tel validation tests?
}
