<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms;

class DefaultTheme extends Theme
{
    public function getViewNamespace() : string
    {
        return 'Nickwest\\EloquentForms';
    }
}
