<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\monthFieldTestInterface;

class montFieldTest extends FieldViewTestCase implements monthFieldTestInterface
{
    protected $test_value = '2016-05';
    protected $test_type = 'month';

    // Run all basic tests
}
