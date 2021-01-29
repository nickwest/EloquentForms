<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\weekFieldTestInterface;

class weekFieldTest extends FieldViewBulmaTestCase implements weekFieldTestInterface
{
    protected $test_value = '2017-W01';
    protected $test_type = 'week';

    // Run all basic tests
}
