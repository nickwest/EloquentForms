<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\datetimeFieldTestInterface;

class datetimeFieldTest extends FieldViewBulmaTestCase implements datetimeFieldTestInterface
{
    protected $test_value = '2016-05-04 15:25:04';
    protected $test_type = 'datetime-local';

    // Run all basic tests
}
