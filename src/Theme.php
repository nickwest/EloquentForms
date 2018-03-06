<?php namespace Nickwest\EloquentForms;

abstract class Theme
{
    abstract public function view_namespace() : string;

    public function __get($key)
    {
        if($key == 'view_namespace')
        {
            return $this->view_namespace();
        }
    }

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
