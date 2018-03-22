<?php

namespace Nickwest\EloquentForms;

interface FormInterface
{
    /**
     * Boot the trait. Adds an observer class for form.
     *
     * @return void
     */
    public static function bootFormTrait();
}
