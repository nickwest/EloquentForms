<?php namespace Nickwest\EloquentForms\Test\ThemeTestInterfaces;
/**
 * The required tests each theme should implement
 */
interface fileFieldTestInterface
{

    public function test_field_has_correct_value_attribute_when_changed();
    public function test_remove_button_can_have_a_different_value();
}
