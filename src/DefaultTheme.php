<?php

namespace Nickwest\EloquentForms;

class DefaultTheme extends Theme
{
    public function getViewNamespace(): string
    {
        return 'Nickwest\\EloquentForms';
    }
}
