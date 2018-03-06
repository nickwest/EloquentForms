<?php namespace Nickwest\EloquentForms;

/*
    Could change this over to use Laravel's Event system, but that would introduce more dependencies
*/

abstract class CustomField
{

    public abstract function makeView(Field $Field, bool $prev_inline = false);

    public function hook_setAllFormValues(Field $Field, $value)
    {
        return;
    }

    public function hook_setPostValues($value)
    {
        throw new NotImplementedException();
    }

}
