<?php
namespace Nickwest\EloquentForms\CustomFields\daysofweek;

use Illuminate\Support\Facades\View;

use Nickwest\EloquentForms\CustomField as BaseCustomField;
use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\DefaultTheme;

class CustomField extends BaseCustomField
{
    /**
     * The Days of the week that we use for storing daysofweek fields
     *
     * @var array
     */
    public $daysofweek = [ 'M' => 'Mon', 'T' => 'Tue', 'W' => 'Wed', 'R' => 'Thu', 'F' => 'Fri', 'S' => 'Sat', 'U' => 'Sun' ];

    public function makeView(Field $Field, bool $prev_inline = false, bool $view_only = false)
    {
        // TODO: make is so themes can override custom fields too.
        return View::make(DefaultTheme::getDefaultNamespace().'::customfields.daysofweek', ['Field' => $Field, 'daysofweek' => $this->daysofweek, 'view_only' => $view_only]);
    }

    public function hook_setAllFormValues(Field $Field, $value)
    {
        $data = explode('|', $value);
        foreach($this->daysofweek as $key => $day) {
            if(in_array($key, $data)) {
                $return[$key] = 1;
            } else {
                $return[$key] = 0;
            }
        }
        return $return;
    }

    public function hook_setPostValues($value)
    {
        return implode('|',$value);
    }


}
