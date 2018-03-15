<?php namespace Nickwest\EloquentForms\Themes\bulma;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Table;

class Theme extends \Nickwest\EloquentForms\Theme
{
    public function getViewNamespace() : string
    {
        return 'Nickwest\\EloquentForms\\bulma';
    }

    public function prepareFieldView(Field &$Field)
    {
        $Field->label_class = 'label';
        switch($Field->attributes->type) {
            case 'text':
            case 'email':
            case 'tel':
            case 'url':
            case 'password':
                $Field->attributes->addClass('input');
            break;
            case 'file':
                $Field->attributes->addClass('file-input');
            break;
            case 'select':
                $Field->input_wrapper_class = 'select';
            break;
            case 'textarea':
                $Field->attributes->addClass('textarea');
            break;

            // These are less than perfect, but Bulma doesn't have unique style for them yet.
            case 'number':
            case 'date':
            case 'datetime-local':
            case 'month':
            case 'time':
            case 'week':
            case 'color':
                $Field->attributes->addClass('input');
            break;
        }

        // Add danger style to fields with errors
        if($Field->error_message) {
            $Field->attributes->addClass('is-danger');
            $Field->input_wrapper_class .= ' is-danger';
        }

        // If there's only one submit button add a is-success class to it

        return;
    }

    /**
     * Modify a Form as necessary
     *
     * @return void
     */
    public function prepareFormView(\Nickwest\EloquentForms\Form &$Form)
    {
        if(count($Form->getSubmitButtons()) == 1){
            foreach($Form->getSubmitButtons() as $Button){
                $Button->attributes->addClass('is-success');
            }
        }

    }

    public function prepareTableView(Table &$Table)
    {
        $Table->attributes->addClass('table');

        return;
    }

}
