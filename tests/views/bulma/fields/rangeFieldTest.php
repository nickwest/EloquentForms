<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\rangeFieldTestInterface;

class rangeFieldTest extends FieldViewBulmaTestCase implements rangeFieldTestInterface
{
    protected $test_value = '55';
    protected $test_type = 'range';
    protected $expected_type_class = '';

    // Run all basic tests

    // TODO: add range validation tests?
}
