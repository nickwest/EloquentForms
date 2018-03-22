<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\CustomFields\daysofweek;

use Nickwest\EloquentForms\Field;
use Illuminate\Support\Facades\View;
use Nickwest\EloquentForms\DefaultTheme;
use Nickwest\EloquentForms\CustomField as BaseCustomField;

class CustomField extends BaseCustomField
{
    /**
     * The Days of the week that we use for storing daysofweek fields.
     *
     * @var array
     */
    public $daysofweek = ['M' => 'Mon', 'T' => 'Tue', 'W' => 'Wed', 'R' => 'Thu', 'F' => 'Fri', 'S' => 'Sat', 'U' => 'Sun'];

    public function makeView(Field $Field, bool $prev_inline = false, bool $view_only = false)
    {
        // TODO: make is so themes can override custom fields too.
        return View::make(DefaultTheme::getDefaultNamespace().'::customfields.daysofweek', ['Field' => $Field, 'daysofweek' => $this->daysofweek, 'view_only' => $view_only]);
    }

    public function hook_setAllFormValues(Field $Field, $value)
    {
        if (is_object($value) || is_array($value)) {
            throw new \Exception('$value cannot be an array or Object');
        }

        $value = explode('|', $value);

        return $value;
    }

    // public function hook_setPostValues($value)
    // {
    //     if(is_array($value)){
    //         return implode('|',$value);
    //     }else{
    //         return $value;
    //     }
    // }
}
