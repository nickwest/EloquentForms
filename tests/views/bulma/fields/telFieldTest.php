<?php namespace Nickwest\EloquentForms\test\views\bulma\fields;

use Nickwest\EloquentForms\test\FieldViewBulmaTestCase;
use Nickwest\EloquentForms\test\ThemeTestInterfaces\telFieldTestInterface;

class telFieldTest extends FieldViewBulmaTestCase implements telFieldTestInterface
{
    protected $test_value = '(206) 555-1212 x505';
    protected $test_type = 'tel';

    // Run all basic tests

    // TODO: add tel validation tests?
}
