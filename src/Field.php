<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use Nickwest\EloquentForms\Attributes;

class Field{

    /**
     * Field Attributes (defaults are set in constructor)
     *
     * @var \Nickwest\EloquentForms\Attributes
     */
    protected $attributes = null;

    /**
     * Human readable formatted name
     *
     * @var string
     */
    protected $label = '';

    /**
     * Suffix for every label (typically ":")
     *
     * @var string
     */
    protected $label_suffix = '';

    /**
     * An example to show by the field
     *
     * @var string
     */
    protected $example = '';

    /**
     * A default value (prepopulated if field is blank)
     *
     * @var string
     */
    protected $default_value = '';

    /**
     * The values when the field allows multiples
     *
     * @var array
     */
    protected $multi_value = [];

    /**
     * Error message to show on the field
     *
     * @var string
     */
    protected $error_message = '';

    /**
     * Blade data to pass through to the subform
     *
     * @var array
     */
    protected $subform_data = [];

    /**
     * Validation rules used by Validator object.
     *
     * @var array
     */
    protected $validation_rules = [];

    /**
     * Blade data to pass through to the subform
     *
     * @var Nickwest\EloquentForms\Form
     */
    protected $subform = null;

    /**
     * Blade data to pass through to the subform
     *
     * @var bool
     */
    protected $is_subform = false;

    /**
     * Options to that are disabled inside of a radio, checkbox or other multi-option field
     *
     * @var array
     */
    protected $disabled_options = [];

    /**
     * A note to display below the field (Accepts HTML markup)
     *
     * @var string
     */
    protected $note;

    /**
     * Should this field be displayed inline?
     *
     * @var bool
     */
    protected $is_inline;

    /**
     * Add a link below the field. Link's name will be equal to field's value
     *
     * @var string
     */
    protected $link = '';

    /**
     * The template that this field should use
     *
     * @var string
     */
    protected $template = '';

    /**
     * Original name when field created
     *
     * @var string
     */
    protected $original_name = '';

    /**
     * Original id when field created
     *
     * @var string
     */
    protected $original_id = '';

    /**
     * Options to populate select, radio, checkbox, and other multi-option fields
     *
     * @var array
     */
    protected $options;

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    protected $container_class = '';

    /**
     * Class(es) for the field's label
     *
     * @var string
     */
    protected $label_class = '';

    /**
     * Class(es) for the input wrapper
     *
     * @var string
     */
    protected $input_wrapper_class = '';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    protected $options_container_class = 'checkbox';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    protected $option_wrapper_class = 'option';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    protected $option_label_class = '';

    /**
     * Value of Delete button for file fields
     *
     * @var string
     */
    protected $delete_button_value = 'Delete';

    /**
     * Class(es) for the field's containing div
     *
     * @var \Nickwest\EloquentForms\Theme
     */
    protected $Theme = null;

    /**
     * Name of the custom field (if this is one)
     *
     * @var \Nickwest\EloquentForms\CustomField
     */
    protected $CustomField = null;

    protected $legacy_properties = [
        'is_required' => 'required',
    ];

    /**
     * Constructor
     *
     * @param string $field_name
     * @param string $type
     * @return void
     */
    public function __construct(string $field_name, string $type = null)
    {
        $this->attributes = new Attributes();

        $this->attributes->name = $field_name;
        $this->attributes->type = $type != null ? $type : 'text';
        $this->attributes->id = 'input-'.$field_name;
        $this->attributes->class = '';

        $this->original_name = $this->attributes->name;
        $this->original_id = $this->attributes->id;
        $this->label = $this->makeLabel();

        // Options for multi-choice fields
        $this->options = [];
        $this->subform_data = [];
    }


//// ACCESSORS AND MUTATORS

    /**
     * Field property and attribute accessor
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if($property == 'view_namespace') {
            return $this->Theme->view_namespace();
        }

        if($this->attributes->attributeExists($property)) {
            return $this->attributes->$property;
        }

        if(property_exists(__CLASS__, $property)) {
            if($property == 'attributes'){
                if(($this->type == 'checkbox' || $this->type == 'radio') && count($this->options) > 1 && $this->multi_key == ''){
                    $this->multi_key = true;
                }
            }
            return $this->{$property};
        }

        return null;
    }

    /**
     * Field property and attribute mutator
     *
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws \Exception
     */
    public function __set(string $property, $value)
    {
        if(isset($this->legacy_properties[$property])){
            $property = $this->legacy_properties[$property];
        }

        if($property == 'options') {
            throw new \Exception('Options must be set with setOption and setOptions methods');
        }

        if(property_exists(__CLASS__, $property)) {
            $this->{$property} = $value;
            return;
        }

        // Whenever setting value, also record the value to $this->multi_value
        if($property == 'value') {
            if($value == '') {
                $this->multi_value = [];
            } else {
                $this->multi_value = is_array($value) ? $value : [$value];
            }
        }

        if(property_exists($this->attributes, $property)){
            $this->attributes->$property = $value;
            return;
        }

        if($this->attributes->attributeExists($property)) {
            $this->setAttribute($property, $value);

            return;
        }

        throw new \Exception('"'.$property.'" is not a valid property.');
    }

    /**
     * Field property isset method
     *
     * @param string $property
     * @return bool
     */
    public function __isset(string $property)
    {
        if($property == 'view_namespace') {
            return true;
        }

        if($this->attributes->attributeExists($property)) {
            return true;
        }

        if(property_exists(__CLASS__, $property)) {
            return true;
        }

        return false;

    }

    public function toJson()
    {
        $array = [
            'attributes' => json_decode($this->attributes->toJson()),
            'label' => $this->label,
            'label_suffix' => $this->label_suffix,
            'example' => $this->example,
            'default_value' => $this->default_value,
            'multi_value' => $this->multi_value,
            'error_message' => $this->error_message,
            'subform_data' => $this->subform_data,
            'subform' => is_object($this->subform) ? json_decode($this->subform->toJson()) : $this->subform,
            'is_subform' => $this->is_subform,
            'disabled_options' => $this->disabled_options,
            'note' => $this->note,
            'is_inline' => $this->is_inline,
            'template' => $this->template,
            'original_name' => $this->original_name,
            'original_id' => $this->original_id,
            'options' => $this->options,
            'container_class' => $this->container_class,
            'label_class' => $this->label_class,
            'input_wrapper_class' => $this->input_wrapper_class,
            'options_container_class' => $this->options_container_class,
            'option_wrapper_class' => $this->option_wrapper_class,
            'option_label_class' => $this->option_label_class,
            'delete_button_value' => $this->delete_button_value,
        ];

        return json_encode($array);
    }

    public function fromJson($json)
    {
        $array = json_decode($json);
        foreach($array as $key => $value) {
            if($key == 'attributes') {
                $Attributes = new Attributes();
                $Attributes->fromJson(json_encode($value));

                $this->$key = $Attributes;
            } elseif(is_object($value)) {
                $this->$key = (array)$value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get the template that this field should use
     *
     * @return string
     */
    public function getTemplate()
    {
        // Use an override template if set
        if($this->template) {
            return $this->template;
        }

        // If this is a radio or checkbox switch between multiples or single
        if($this->attributes->type == 'checkbox' && is_array($this->options)) {
            if($this->view_namespace != '' && View::exists($this->view_namespace.'::fields.checkboxes')) {
                return $this->view_namespace.'::fields.checkboxes';
            }
            return 'form-maker::fields.checkboxes';
        }

        // If this is a radio or checkbox switch between multiples or single
        if($this->attributes->type == 'radio' && is_array($this->options)) {
            if($this->view_namespace != '' && View::exists($this->view_namespace.'::fields.radios')) {
                return $this->view_namespace.'::fields.radios';
            }
            return 'form-maker::fields.radios';
        }

        if($this->view_namespace != '' && View::exists($this->view_namespace.'::fields.'.$this->attributes->type)) {
            return $this->view_namespace.'::fields.'.$this->attributes->type;
        }
        return 'form-maker::fields.'.$this->attributes->type;
    }

    /**
     * Set a Field attribute
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $attribute, $value)
    {
        if($this->attributes->attributeExists($attribute)) {
            $this->attributes->$attribute = $value;
            return;
        }

        throw new \Exception('"'.$attribute.'" is not a valid attribute.');
    }

    /**
     * Get an attribute of the Field
     *
     * @param string $attribute
     * @return string
     * @throws \Exception
     */
    public function getAttribute(string $attribute)
    {
        return $this->attributes->$attribute;
    }

    /**
     * Add a css class to the attributes
     *
     * @param string $class
     * @return void
     */
    public function addClass(string $class)
    {
        if(trim($class) != '') {
            $this->attributes->addClass($class);
        }
    }

    /**
     * Remove a cs  class to the attributes
     *
     * @param string $class
     * @return void
     */
    public function removeClass(string $class)
    {
        $this->attributes->removeClass($class);
    }

    /**
     * Add, change, or remove an option
     *
     * @param string $key
     * @param string $value
     * @return void
     * @throws \Exception
     */
    public function setOption(string $key, string $value)
    {
        if($value == null) {
            unset($this->options[$key]);
            return;
        }

        if(is_array($value) || is_object($value) || is_resource($value)) {
            throw new \Exception('Option values must text');
        }

        $this->options[$key] = $value;
    }

    /**
     * Set options replacing all current options with those in the given array
     *
     * @param mixed $options
     * @return void
     * @throws \Exception
     */
    public function setOptions($options)
    {
        if($options == null) {
            $this->options = [];
            return;
        }

        if(!is_array($options)) {
            throw new \Exception('$options must be an array or null');
        }

        foreach($options as $key => $value) {
            if(is_array($value) || is_object($value) || is_resource($value)) {
                throw new \Exception('Option values must text');
            }

            $this->options[$key] = $value;
        }
    }

    /**
     * Return the formatted value of the Field's value
     *
     * @return string
     */
    public function getFormattedValue()
    {
        return $this->formatValue($this->value);
    }


//// View Methods


    /**
     * Make a form view for this field
     *
     * @var bool $prev_inline Was the previous field inline?
     * @var bool $view_only
     * @return View
     */
    public function makeView(bool $prev_inline = false, bool $view_only = false)
    {
        if($this->error_message) {
            $this->addClass('error');
        }

        $this->Theme->prepareFieldView($this);

        if(is_object($this->CustomField)) {
            return $this->CustomField->makeView($this, $prev_inline, $view_only);
        }

        return View::make($this->getTemplate(), ['Field' => $this, 'prev_inline' => $prev_inline, 'view_only' => $view_only]);
    }

    /**
     * Make a display only view for this field
     *
     * @return View
     */
    public function makeDisplayView(bool $prev_inline = false)
    {
        $this->Theme->prepareFieldView($this);
        if($this->view_namespace != '' && View::exists($this->view_namespace.'::fields.display')) {
            return View::make($this->view_namespace.'::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
        }

        return View::make('form-maker::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
    }

    /**
     * Make an option view for this field
     *
     * @param string $key
     * @param bool $view_only
     * @return View
     */
    public function makeOptionView(string $key, bool $view_only = false)
    {
        $this->attributes->id = $this->original_id.'-'.$key;
        $this->attributes->value = $key;

        $this->attributes->checked = in_array($key, $this->multi_value) ? true : false;

        $this->Theme->prepareFieldView($this);

        if($this->view_namespace != '' && View::exists($this->view_namespace.'::fields.'.$this->attributes->type.'_option')) {
            return View::make($this->view_namespace.'::fields.'.$this->attributes->type.'_option', array('Field' => $this, 'key' => $key, 'view_only' => $view_only));
        }
        return View::make('form-maker::fields.'.$this->attributes->type.'_option', array('Field' => $this, 'key' => $key, 'view_only' => $view_only));
    }


//// HELPERS

    // TODO: Add isValid() method that uses validation_rules and Validator


    /**
     * Make sure the field has all required options and stuff set
     *
     * @return void
     * @throws \Exception
     */
    public function validateFieldStructure()
    {
        switch($this->attribute['type']) {
            // TODO: Expand on this so it's more comprehensive

            case 'select':
                if(!is_array($this->options) || count($this->options) == 0) {
                    throw new \Exception('Field validation error: Field "'.$this->attributes->name.'" must have options set');
                }
        }
    }

    /**
     * Make a label for the given field, uses $this->label if available, otherwises generates based on field name
     *
     * @return string
     */
    protected function makeLabel()
    {
        // If no label use the field's name, but replace _ with spaces
        if (trim($this->label) == '') {
            // If this is changed Table::getLabel() should be updated to match
            $this->label = ucfirst(str_replace('_', ' ', $this->attributes->name));
        }

        return $this->label;
    }

    /**
     * Return the formatted value of the $value
     *
     * @param string $value
     * @return string
     */
    protected function formatValue(string $value)
    {
        if(is_array($this->options) && isset($this->options[$value])) {
            return $this->options[$value];
        }

        // TODO: Add other formatting options here, specifically for dates

        return $value;
    }

}
