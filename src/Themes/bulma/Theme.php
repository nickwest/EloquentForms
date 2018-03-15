<?php namespace Nickwest\EloquentForms\Themes\bulma;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Table;

class Theme extends \Nickwest\EloquentForms\Theme
{
    /**
     * Get the View namespace for this Theme
     *
     * @return string
     */
    public function getViewNamespace(): string
    {
        return 'Nickwest\\EloquentForms\\bulma';
    }

    /**
     * Modify the field view
     *
     * @param Nickwest\EloquentForms\Field
     * @return void
     */
    public function prepareFieldView(Field &$Field): void
    {
        $Field->label_class = 'label';

        $this->setTypeClasses();
        $this->setErrorClasses();
    }

    /**
     * Modify a Form as necessary
     *
     * @return void
     */
    public function prepareFormView(\Nickwest\EloquentForms\Form &$Form): void
    {
        if(count($Form->getSubmitButtons()) == 1){
            foreach($Form->getSubmitButtons() as $Button){
                $Button->attributes->addClass('is-success');
            }
        }

    }

    /**
     * Modify the table view as necessary
     *
     * @param Nickwest\EloquentForms\Table
     * @return void
     */
    public function prepareTableView(Table &$Table): void
    {
        $Table->attributes->addClass('table');

        return;
    }

    /**
     * Add is-danger class to fields with errors
     *
     * @param Nickwest\EloquentForms\Field $Field
     * @return void
     */
    protected function setErrorClasses(Field &$Field): void
    {
        if($Field->error_message) {
            $Field->attributes->addClass('is-danger');
            $Field->input_wrapper_class .= ' is-danger';
        }
    }

    /**
     * Set type based classes to Field
     *
     * @param Nickwest\EloquentForms\Field $Field
     * @return void
     */
    public function setTypeClasses(Field &$Field): void
    {
        switch($Field->attributes->type) {
            case 'file':
                $Field->attributes->addClass('file-input');
                break;
            case 'select':
                $Field->input_wrapper_class = 'select';
                break;
            case 'textarea':
                $Field->attributes->addClass('textarea');
                break;
            default:
                $Field->attributes->addClass('input');
                break;
        }
    }

}
