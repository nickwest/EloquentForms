<?php

namespace Nickwest\EloquentForms;

abstract class Theme
{
    /**
     * Get this Theme's namespace.
     *
     * @return string
     */
    abstract public function getViewNamespace() : string;

    /**
     * Modify a field as necessary.
     *
     * @return void
     */
    public function prepareFieldView(\Nickwest\EloquentForms\Field &$Field)
    {
    }

    /**
     * Modify a field as necessary.
     *
     * @return void
     */
    public function prepareFieldOptionView(\Nickwest\EloquentForms\Field &$Field)
    {
    }

    /**
     * Modify a field as necessary.
     *
     * @return void
     */
    public function prepareFormView(\Nickwest\EloquentForms\Form &$Form)
    {
    }

    /**
     * Modify a table as necessary.
     *
     * @return void
     */
    public function prepareTableView(\Nickwest\EloquentForms\Table &$Table)
    {
    }

    /**
     * Get the default namespace.
     *
     * @return string
     */
    public static function getDefaultNamespace(): string
    {
        return 'Nickwest\\EloquentForms';
    }
}
