<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\monthFieldTestInterface;

class monthFieldTest extends FieldViewBulmaTestCase implements monthFieldTestInterface
{
    protected $test_value = '2016-05';
    protected $test_type = 'month';

    // Run all basic tests
}
