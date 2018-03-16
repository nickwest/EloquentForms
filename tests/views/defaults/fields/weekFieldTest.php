<?php namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\weekFieldTestInterface;

class weekFieldTest extends FieldViewTestCase implements weekFieldTestInterface
{
    protected $test_value = '2017-W01';
    protected $test_type = 'week';

    // Run all basic tests
}
