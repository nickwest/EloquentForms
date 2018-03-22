<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\dateFieldTestInterface;

class dateFieldTest extends FieldViewBulmaTestCase implements dateFieldTestInterface
{
    protected $test_value = '2016-05-04';
    protected $test_type = 'date';

    // Run all basic tests
}
