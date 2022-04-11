<?php

namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;

/**
 * The required tests each theme should implement
 */
interface selectFieldTestInterface
{
    public function test_field_has_all_possible_options();
    public function test_field_can_have_multiple_attribute_set();
    public function test_field_has_proper_option_selected_when_value_is_set();
    public function test_field_can_have_multiple_values();
    public function test_field_has_correct_value_attribute();
    public function test_field_has_correct_value_attribute_when_changed();
}
