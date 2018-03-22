<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\timeFieldTestInterface;

class timeFieldTest extends FieldViewBulmaTestCase implements timeFieldTestInterface
{
    protected $test_value = '15:44';
    protected $test_type = 'time';

    // Run all basic tests
}
