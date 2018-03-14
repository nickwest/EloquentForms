<?php namespace Nickwest\EloquentForms;

abstract class Theme
{
    /**
     * Get this Theme's namespace
     *
     * @return string
     */
    abstract public function getViewNamespace() : string;

    /**
     * Modify a field as necessary
     *
     * @return void
     */
    public function prepareFieldView(\Nickwest\EloquentForms\Field &$Field)
    {
        return;
    }

    /**
     * Modify a field as necessary
     *
     * @return void
     */
    public function prepareFieldOptionView(\Nickwest\EloquentForms\Field &$Field)
    {
        return;
    }

    /**
     * Modify a field as necessary
     *
     * @return void
     */
    public function prepareFormView(\Nickwest\EloquentForms\Form &$Form)
    {
        return;
    }

    /**
     * Modify a table as necessary
     *
     * @return void
     */
    public function prepareTableView(\Nickwest\EloquentForms\Table &$Table)
    {
        return;
    }

    /**
     * Get the default namespace
     *
     * @return string
     */
    static public function getDefaultNamespace(): string
    {
        return 'Nickwest\\EloquentForms';
    }

}
