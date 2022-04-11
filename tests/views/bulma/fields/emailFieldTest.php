<?php

namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\emailFieldTestInterface;

class emailFieldTest extends FieldViewBulmaTestCase implements emailFieldTestInterface
{
    protected $test_value = 'test@example.org';
    protected $test_type = 'email';

    // Run all basic tests
}
