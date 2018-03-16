<?php namespace Nickwest\EloquentForms\Test\views\bulma\fields;

use Nickwest\EloquentForms\Test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\monthFieldTestInterface;

class monthFieldTest extends FieldViewBulmaTestCase implements monthFieldTestInterface
{
    protected $test_value = '2016-05';
    protected $test_type = 'month';

    // Run all basic tests
}
