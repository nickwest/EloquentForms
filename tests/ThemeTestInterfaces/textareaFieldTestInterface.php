<?php

namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;

/**
 * The required tests each theme should implement
 */
interface textareaFieldTestInterface
{
    public function test_field_has_correct_type_attribute();
    public function test_field_has_correct_value_attribute();
    public function test_field_has_correct_value_attribute_when_changed();
}
