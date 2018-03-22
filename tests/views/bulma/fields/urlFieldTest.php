<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\urlFieldTestInterface;

class urlFieldTest extends FieldViewBulmaTestCase implements urlFieldTestInterface
{
    protected $test_value = 'https://www.google.com';
    protected $test_type = 'url';

    // Run all basic tests
}
