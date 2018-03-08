<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use Nickwest\EloquentForms\Attributes;
use Nickwest\EloquentForms\Exceptions\OptionValueException;

class Field{

    /**
     * Field Attributes (defaults are set in constructor)
     *
     * @var \Nickwest\EloquentForms\Attributes
     */
    public $Attributes = null;

    /**
     * Class(es) for the field's containing div
     *
     * @var \Nickwest\EloquentForms\Theme
     */
    public $Theme = null;

    /**
     * Blade data to pass through to the subform
     *
     * @var Nickwest\EloquentForms\Form
     */
    public $Subform = null;

    /**
     * Name of the custom field (if this is one)
     *
     * @var \Nickwest\EloquentForms\CustomField
     */
    public $CustomField = null;


    /**
     * Human readable formatted name
     *
     * @var string
     */
    public $label = '';

    /**
     * Suffix for every label (typically ":")
     *
     * @var string
     */
    public $label_suffix = '';

    /**
     * An example to show by the field
     *
     * @var string
     */
    public $example = '';

    /**
     * A note to display below the field (Accepts HTML markup)
     *
     * @var string
     */
    public $note;

    /**
     * Add a link below the field. Link's name will be equal to field's value
     *
     * @var string
     */
    public $link = '';

    /**
     * Error message to show on the field
     *
     * @var string
     */
    public $error_message = '';

    /**
     * A default value (prepopulated if field is blank)
     *
     * @var string
     */
    public $default_value = null;

    /**
     * Should this field be displayed inline?
     *
     * @var bool
     */
    public $is_inline;

    /**
     * Validation rules used by Validator object.
     *
     * @var array
     */
    public $validation_rules = [];


    /**
     * Class(es) for the field's label
     *
     * @var string
     */
    public $label_class = '';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    public $container_class = '';

    /**
     * Class(es) for the input wrapper
     *
     * @var string
     */
    public $input_wrapper_class = '';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    public $options_container_class = ''; // was 'checkbox'

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    public $option_wrapper_class = 'option';

    /**
     * Class(es) for the field's containing div
     *
     * @var string
     */
    public $option_label_class = '';



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
    protected $options = [];

    /**
     * Options to that are disabled inside of a radio, checkbox or other multi-option field
     *
     * @var array
     */
    protected $disabled_options = [];


    /**
     * Constructor
     *
     * @param string $field_name
     * @param string $type
     * @return void
     */
    public function __construct(string $field_name, string $type = null)
    {
        $this->Attributes = new Attributes();

        // Set some base attributes for the field
        $this->Attributes->name = $field_name;
        $this->Attributes->type = $type != null ? $type : 'text';
        $this->Attributes->id = $field_name;
        $this->Attributes->class = '';

        // Set some basic properties
        $this->original_name = $this->Attributes->name;
        $this->original_id = $this->Attributes->id;
        $this->label = $this->makeLabel();

        // TODO: Make config and set default theme in config
        $this->Theme = new DefaultTheme();
    }


//// ACCESSORS AND MUTATORS

    /**
     * Get the View Namespace
     *
     * @return string
     */
    public function getViewNamespace(): string
    {
        return $this->Theme->view_namespace();
    }

    /**
     * Is this field a subform?
     *
     * @return bool
     */
    public function isSubform(): bool
    {
        return is_object($this->Subform);
    }

    /**
     * Get the original name of this field
     *
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->original_name;
    }

    /**
     * Get the original name of this field
     *
     * @return string
     */
    public function getOriginalId(): string
    {
        return $this->original_id;
    }


    /**
     * Convert this object to a simple JSON representation
     *
     * @return string Json
     */
    public function toJson(): string
    {
        $array = [
            'Attributes' => json_decode($this->Attributes->toJson()),
            'Theme' => (is_object($this->Theme) ? '\\'.get_class($this->Theme) : null),
            'Subform' => is_object($this->Subform) ? $this->Subform->toJson() : $this->Subform,
            'CustomField' => (is_object($this->CustomField) ? serialize($this->CustomField) : $this->CustomField),
            'label' => $this->label,
            'label_suffix' => $this->label_suffix,
            'example' => $this->example,
            'note' => $this->note,
            'link' => $this->link,
            'error_message' => $this->error_message,
            'default_value' => $this->default_value,
            'is_inline' => $this->is_inline,
            'validation_rules' => $this->validation_rules,
            'label_class' => $this->label_class,
            'container_class' => $this->container_class,
            'input_wrapper_class' => $this->input_wrapper_class,
            'options_container_class' => $this->options_container_class,
            'option_wrapper_class' => $this->option_wrapper_class,
            'option_label_class' => $this->option_label_class,
            'original_name' => $this->original_name,
            'original_id' => $this->original_id,
            'options' => $this->options,
            'disabled_options' => $this->disabled_options,
        ];

        return json_encode($array);
    }

    /**
     * Populate Field from Json representation
     *
     * @param string $json
     * @return void
     */
    public function fromJson($json)
    {
        $array = json_decode($json);
        foreach($array as $key => $value) {
            if($key == 'Attributes') {
                $this->Attributes = new Attributes();
                $this->Attributes->fromJson(json_encode($value));

            } elseif($key == 'Theme' && $value !== null) {
                $this->Theme = new $value(); // TODO: make a to/from JSON method on this? is it necessary?

            } elseif($key == 'Subform' && $value !== null) {
                $this->Subform = new Form();
                $this->Subform->fromJson($value);

            } elseif($key == 'CustomField') {
                $this->CustomField = unserialize($value); // TODO: make a to/from JSON method on this? is it necessary?

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
    public function getTemplate(): string
    {
        // If this is a radio or checkbox switch between multiples or single
        if($this->Attributes->type == 'checkbox' && is_array($this->options)) {
            if($this->getViewNamespace() != '' && View::exists($this->getViewNamespace().'::fields.checkboxes')) {
                return $this->getViewNamespace().'::fields.checkboxes';
            }
            return 'Nickwest\\EloquentForms::fields.checkboxes';
        }

        // If this is a radio or checkbox switch between multiples or single
        if($this->Attributes->type == 'radio' && is_array($this->options)) {
            if($this->getViewNamespace() != '' && View::exists($this->getViewNamespace().'::fields.radios')) {
                return $this->getViewNamespace().'::fields.radios';
            }
            return 'Nickwest\\EloquentForms::fields.radios';
        }

        if($this->getViewNamespace() != '' && View::exists($this->getViewNamespace().'::fields.'.$this->Attributes->type)) {
            return $this->getViewNamespace().'::fields.'.$this->Attributes->type;
        }
        return 'Nickwest\\EloquentForms::fields.'.$this->Attributes->type;
    }

    /**
     * Add or change an option
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setOption(string $key, string $value)
    {
        if($value == null) {
            unset($this->options[$key]);
            return;
        }

        $this->options[$key] = $value;
    }

    /**
     * Remove an option
     *
     * @param string $key
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function removeOption(string $key)
    {
        if(!isset($this->options[$key])){
            throw new OptionValueException('Cannot disable '.$key.'. It\'s not currently in the options array');
        }

        unset($this->options[$key]);
    }

    /**
     * Returns options set to this field
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set options replacing all current options with those in the given array
     *
     * @param array $options
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function setOptions(array $options)
    {
        if($options == null) {
            $this->options = [];
            return;
        }

        foreach($options as $key => $value) {
            if(is_array($value) || is_object($value) || is_resource($value)) {
                throw new OptionValueException('Option values must be strings');
            }

            $this->options[$key] = $value;
        }
    }

    /**
     * Set options that should be disabled on the input field
     *
     * @param array $options
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function setDisabledOptions(array $options)
    {
        $this->disabled_options = [];

        if($options == null) {
            return;
        }

        foreach($options as $key) {
            if(!isset($this->options[$key])){
                throw new OptionValueException('Cannot disable '.$key.'. It\'s not currently in the options array');
            }

            $this->disabled_options[] = $key;
        }
    }

    /**
     * Return the formatted value of the Field's value
     *
     * @return string
     */
    public function getFormattedValue(): string
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
            $this->Attributes->addClass('error');
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

        if($this->getViewNamespace() != '' && View::exists($this->getViewNamespace().'::fields.display')) {
            return View::make($this->getViewNamespace().'::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
        }

        return View::make('Nickwest\\EloquentForms::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
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
        $this->Attributes->id = $this->original_id.'-'.$key;
        $this->Attributes->value = $key;

        $this->Attributes->checked = in_array($key, $this->multi_value) ? true : false;

        $this->Theme->prepareFieldView($this);

        if($this->getViewNamespace() != '' && View::exists($this->getViewNamespace().'::fields.'.$this->Attributes->type.'_option')) {
            return View::make($this->getViewNamespace().'::fields.'.$this->Attributes->type.'_option', array('Field' => $this, 'key' => $key, 'view_only' => $view_only));
        }
        return View::make('Nickwest\\EloquentForms::fields.'.$this->Attributes->type.'_option', array('Field' => $this, 'key' => $key, 'view_only' => $view_only));
    }


//// HELPERS

    // TODO: Add isValid() method that uses validation_rules and Validator

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
            $this->label = ucfirst(str_replace('_', ' ', $this->Attributes->name));
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
