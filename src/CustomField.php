<?php

namespace Nickwest\EloquentForms;

// TODO: Change this over to use Laravel's Event system

use Nickwest\EloquentForms\Exceptions\NotImplementedException;

abstract class CustomField
{
    abstract public function makeView(Field $Field, bool $prev_inline = false);

    /**
     * Hook for FormTrait::setAllFormValues allowing CustomField to modify value
     *
     * @param Nickwest\EloquentForms\Field $Field
     * @param mixed $value
     *
     * @throws Nickwest\EloquentForms\Exceptions\NotImplementedException;
     */
    public function hook_setAllFormValues(Field $Field, $value)
    {
        throw new NotImplementedException();
    }

    /**
     * Hook for FormTrait::setPostValues allowing CustomField to modify value
     *
     * @param mixed $value
     *
     * @throws Nickwest\EloquentForms\Exceptions\NotImplementedException;
     */
    public function hook_setPostValues($value)
    {
        throw new NotImplementedException();
    }
}
