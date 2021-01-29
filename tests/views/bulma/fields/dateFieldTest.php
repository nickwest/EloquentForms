<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\dateFieldTestInterface;

class dateFieldTest extends FieldViewBulmaTestCase implements dateFieldTestInterface
{
    protected $test_value = '2016-05-04';
    protected $test_type = 'date';

    // Run all basic tests
}
