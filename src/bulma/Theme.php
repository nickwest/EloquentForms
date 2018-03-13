<?php namespace Nickwest\EloquentForms\bulma;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Table;

class Theme extends \Nickwest\EloquentForms\Theme
{
    public function getViewNamespace() : string
    {
        return 'Nickwest\\EloquentForms';
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


        return;
    }

    public function prepareTableView(Table &$Table)
    {
        $Table->attributes->addClass('table');

        return;
    }

}
