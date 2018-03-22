<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\timeFieldTestInterface;

class timeFieldTest extends FieldViewTestCase implements timeFieldTestInterface
{
    protected $test_value = '15:44';
    protected $test_type = 'time';

    // Run all basic tests
}
