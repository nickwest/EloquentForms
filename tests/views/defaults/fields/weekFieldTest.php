<?php namespace Nickwest\EloquentForms\test\views\defaults\fields;

use Nickwest\EloquentForms\test\FieldViewTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\weekFieldTestInterface;

class weekFieldTest extends FieldViewTestCase implements weekFieldTestInterface
{
    protected $test_value = '2017-W01';
    protected $test_type = 'week';

    // Run all basic tests
}
