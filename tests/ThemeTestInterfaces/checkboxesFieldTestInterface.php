<?php

namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;

/**
 * The required tests each theme should implement
 */
interface checkboxesFieldTestInterface
{
    public function test_field_has_correct_id_attribute();
    public function test_field_has_correct_id_attribute_when_changed();
    public function test_field_has_correct_id_attribute_when_prefix_changed();
    public function test_field_has_correct_value_attribute();
    public function test_field_has_proper_label();
    public function test_field_has_proper_label_when_attributes_changed();
    public function test_field_has_selected_attribute_when_value_is_equal();
    public function test_field_has_correct_value_attribute_when_changed();
    public function test_field_can_have_multiple_values();
    public function test_fields_have_brackets_in_name_when_multiple_options_are_set();
}
