<?php namespace Nickwest\EloquentForms\test\ThemeTestInterfaces;
/**
 * The required tests each theme should implement
 */
interface datalistFieldTestInterface
{
    public function test_field_not_there_if_no_options_set();
    public function test_field_works_if_options_are_set();
    public function test_field_options_in_datalist_are_as_expected();
}
