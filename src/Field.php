<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use Nickwest\EloquentForms\Attributes;
use Nickwest\EloquentForms\Traits\Themeable;
use Nickwest\EloquentForms\Exceptions\OptionValueException;

class Field{

    use Themeable;

    /**
     * Field Attributes (defaults are set in constructor)
     *
     * @var Nickwest\EloquentForms\Attributes
     */
    public $attributes = null;

    /**
     * Blade data to pass through to the subform
     *
     * @var Nickwest\EloquentForms\Form
     */
    public $Subform = null;

    /**
     * Name of the custom field (if this is one)
     *
     * @var Nickwest\EloquentForms\CustomField
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
    public $validation_rules = '';

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
     * Blade data to be passed to subForm
     *
     * @var string
     */
    public $subform_data = [];

    /**
     * The name of the delete button on a file field when there is a file
     *
     * @var string
     */
    public $file_delete_button_value = 'Remove';

    /**
     * Extra stuff accessible in blade templates, used by certain field types
     *
     * @var array
     */
    public $extra_blade_data = [];

    /**
     * Original name when field created
     *
     * @var string
     */
    protected $original_name = '';

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
        $this->attributes = new Attributes();
        $this->attributes->id_prefix = 'input-';

        // Set some base attributes for the field
        $this->attributes->name = $field_name;
        $this->attributes->type = $type != null ? $type : 'text';
        $this->attributes->id = $field_name;
        $this->attributes->class = '';
        $this->attributes->value = null;

        // Set some basic properties
        $this->original_name = $this->attributes->name;
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
        return $this->Theme->getViewNamespace();
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
        return $this->attributes->getRawID();
    }


    /**
     * Convert this object to a simple JSON representation
     *
     * @return string Json
     */
    public function toJson(): string
    {
        $array = [
            'attributes' => json_decode($this->attributes->toJson()),
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
            'validation_rules' => serialize($this->validation_rules),
            'label_class' => $this->label_class,
            'container_class' => $this->container_class,
            'input_wrapper_class' => $this->input_wrapper_class,
            'options_container_class' => $this->options_container_class,
            'option_wrapper_class' => $this->option_wrapper_class,
            'option_label_class' => $this->option_label_class,
            'original_name' => $this->original_name,
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
    public function fromJson($json): void
    {
        $array = json_decode($json);
        foreach($array as $key => $value) {
            switch($key){
                case 'attributes':
                    $this->attributes = new Attributes();
                    $this->attributes->fromJson(json_encode($value));
                    break;
                case 'Theme' == $key && $value !== null:
                    $this->Theme = new $value(); // TODO: make a to/from JSON method on this? is it necessary?
                    break;
                case 'Subform' == $key && $value !== null:
                    $this->Subform = new Form();
                    $this->Subform->fromJson($value);
                    break;
                case 'CustomField':
                case 'validation_rules':
                    $this->$key = unserialize($value);
                    break;
                case 'options':
                    foreach($value as $key => $value){
                        $this->options[(string)$key] = $value;
                    }
                    break;
                case is_object($value):
                    $this->$key = (array)$value;
                    break;
                default:
                    $this->$key = $value;
                    break;
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
        $namespace = 'Nickwest\\EloquentForms'; // TODO: Define the default name space somewhere?

        // Get the template name
        $template = 'fields.'.$this->attributes->type;
        if($this->attributes->type == 'checkbox' && is_array($this->options)){
            $template = 'fields.checkboxes';
        }elseif($this->attributes->type == 'radio' && is_array($this->options)){
            $template = 'fields.radios';
        }

        // Check if the theme has an override for the template, if so use the Theme namespace
        if(View::exists($this->getViewNamespace().'::'.$template)){
            $namespace = $this->getViewNamespace();
        }

        return $namespace.'::'.$template;
    }

    /**
     * Add or change an option
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setOption(string $key, string $value): void
    {
        if($value == null) {
            unset($this->options[$key]);
            return;
        }

        $this->options[$key] = $value;
    }

    /**
     * Returns options set to this field
     *
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key)
    {
        return $this->options[$key];
    }

    /**
     * Checks if an option key is set
     *
     * @param string $key
     * @return bool
     */
    public function hasOption($key): bool
    {
        return isset($this->options[$key]);
    }

    /**
     * Remove an option
     *
     * @param string $key
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function removeOption(string $key): void
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
    public function setOptions(array $options): void
    {
        if($options == null) {
            $this->options = [];
            return;
        }

        foreach($options as $key => $value) {
            if(is_array($value) || is_object($value) || is_resource($value)) {
                throw new OptionValueException('Option values must be strings');
            }

            $this->setOption($key, $value);
        }
    }

    /**
     * Set options that should be disabled on the input field
     *
     * @param array $options
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function setDisabledOptions(array $options): void
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
     * @return Illuminate\View\View
     */
    public function makeView(bool $prev_inline = false, bool $view_only = false): \Illuminate\View\View
    {
        if($this->error_message) {
            $this->attributes->addClass('error');
        }

        $this->Theme->prepareFieldView($this);

        if(is_object($this->CustomField)) {
            return $this->CustomField->makeView($this, $prev_inline, $view_only);
        }

        // Never output the password value, ever.
        if($this->attributes->type == 'password'){
            $this->attributes->value = null;
        }elseif($this->attributes->type == 'textarea'){
            $this->extra_blade_data['value'] = $this->attributes->value;
            unset($this->attributes->value);
        }

        return View::make($this->getTemplate(), ['Field' => $this, 'prev_inline' => $prev_inline, 'view_only' => $view_only]);
    }

    /**
     * Make a display only view for this field
     *
     * @return Illuminate\View\View
     */
    public function makeDisplayView(bool $prev_inline = false): \Illuminate\View\View
    {
        $this->Theme->prepareFieldView($this);

        if(View::exists($this->getViewNamespace().'::fields.display')) {
            return View::make($this->getViewNamespace().'::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
        }

        return View::make(DefaultTheme::getDefaultNamespace().'::fields.display', ['Field' => $this, 'prev_inline' => $prev_inline]);
    }

    /**
     * Make an option view for this field
     *
     * @param string $key
     * @param mixed $value
     * @param bool $view_only
     * @return Illuminate\View\View
     */
    public function makeOptionView(string $key, $value, bool $view_only = false): \Illuminate\View\View
    {
        // Clone the field & attributes so we don't screw up it's properties when making the option
        $Field = clone $this;
        $Field->attributes = clone $Field->attributes;

        $Field->attributes->id_suffix = '-'.$key;
        $Field->attributes->value = $key;

        if($key == $value){
            $Field->attributes->checked = null;
        }

        $Field->Theme->prepareFieldView($Field);

        if($Field->getViewNamespace() != '' && View::exists($Field->getViewNamespace().'::fields.'.$Field->attributes->type.'_option')) {
            return View::make($Field->getViewNamespace().'::fields.'.$Field->attributes->type.'_option', array('Field' => $Field, 'key' => $key, 'view_only' => $view_only));
        }
        return View::make('Nickwest\\EloquentForms::fields.'.$Field->attributes->type.'_option', array('Field' => $Field, 'key' => $key, 'view_only' => $view_only));
    }


//// HELPERS

    // TODO: Add isValid() method that uses validation_rules and Validator

    /**
     * Make a label for the given field, uses $this->label if available, otherwises generates based on field name
     *
     * @return string
     */
    protected function makeLabel(): string
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
    protected function formatValue(string $value): string
    {
        if(is_array($this->options) && isset($this->options[$value])) {
            return $this->options[$value];
        }

        // TODO: Add other formatting options here, specifically for dates

        return $value;
    }

}
