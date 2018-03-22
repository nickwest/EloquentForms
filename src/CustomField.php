<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms;

// TODO: Change this over to use Laravel's Event system

use Nickwest\EloquentForms\Exceptions\NotImplementedException;

abstract class CustomField
{
    abstract public function makeView(Field $Field, bool $prev_inline = false);

    public function hook_setAllFormValues(Field $Field, $value): void
    {
    }

    public function hook_setPostValues($value): void
    {
        throw new NotImplementedException();
    }
}
