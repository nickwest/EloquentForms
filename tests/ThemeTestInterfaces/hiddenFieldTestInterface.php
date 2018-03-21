<?php namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;
/**
 * The required tests each theme should implement
 */
interface hiddenFieldTestInterface
{
    public function test_field_has_proper_label();
    public function test_field_has_proper_label_when_attributes_changed();
    public function test_field_has_proper_label_when_label_changed();
    public function test_field_has_proper_label_suffix_when_set();
    public function test_field_has_example_when_set();
    public function test_field_has_note_when_set();
    public function test_field_has_a_container_div();
    public function test_field_container_div_has_valid_attributes();
}
