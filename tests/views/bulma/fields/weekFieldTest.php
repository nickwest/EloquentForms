<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\weekFieldTestInterface;

class weekFieldTest extends FieldViewBulmaTestCase implements weekFieldTestInterface
{
    protected $test_value = '2017-W01';
    protected $test_type = 'week';

    // Run all basic tests
}
