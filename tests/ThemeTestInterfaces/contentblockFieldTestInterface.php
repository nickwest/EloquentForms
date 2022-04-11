<?php

namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;

/**
 * The required tests each theme should implement
 */
interface contentblockFieldTestInterface
{
    public function test_field_not_there_if_no_value();
    public function test_field_has_correct_id_attribute();
    public function test_field_has_correct_id_attribute_when_changed();
    public function test_field_has_correct_id_attribute_when_prefix_changed();
    public function test_field_has_correct_class_attribute();
    public function test_field_has_correct_class_attribute_when_one_class_added();
    public function test_field_has_correct_class_attribute_when_many_classes_added();
    public function test_field_has_correct_class_attribute_when_classes_removed();
    public function test_field_has_proper_content();
    public function test_field_has_a_container_div();
    public function test_field_container_div_has_valid_attributes();
}
