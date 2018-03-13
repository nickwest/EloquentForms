<?php namespace Nickwest\EloquentForms;

abstract class Theme
{
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
    public function prepareFormView(\Nickwest\EloquentForms\Field &$Field)
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

}
