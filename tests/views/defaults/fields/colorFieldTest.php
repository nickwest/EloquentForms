<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\colorFieldTestInterface;

class colorFieldTest extends FieldViewTestCase implements colorFieldTestInterface
{
    protected $test_value = '#ff00ff';
    protected $test_type = 'color';

    // Run all basic tests
}
