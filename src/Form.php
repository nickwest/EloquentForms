<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use Nickwest\EloquentForms\DefaultTheme;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;
use Nickwest\EloquentForms\Exceptions\InvalidCustomFieldObjectException;

class Form{
    /**
     * Use Laravel csrf_field() method for creating a CSRF field in the form?
     * Note: This will elegantly fail if the csrf_field() method is not available.
     *
     * @var bool
     */
    public $laravel_csrf = true;

    /**
     * Submit Button name (used for first submit button only)
     *
     * @var Nickwest\EloquentForms\Attributes
     */
    public $attributes = null;

    /**
     * Array of Field Objects
     *
     * @var array
     */
    protected $Fields = [];

    /**
     * Array of field_names to display
     *
     * @var array
     */
    protected $display_fields = [];

    /**
     * Array of Field Objects
     *
     * @var array
     */
    protected $SubmitFields = [];

    /**
     * Theme to use
     *
     * @var string
     */
    protected $Theme = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        // Instantiate objects
        $this->Theme = new DefaultTheme();
        $this->attributes = new Attributes;

        // Set the action to default to the current path
        $this->attributes->action = Request::url();
        $this->attributes->method = 'POST';

        $this->addSubmitButton('submit_button', 'Submit');
    }

    /**
     * Field value accessor
     *
     * @param string $field_name
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return Nickwest\EloquentForms\Field
     */
    public function __get(string $field_name): Field
    {
        return $this->getField($field_name);
    }

     /**
     * Field value isset
     *
     * @param string $field_name
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return bool
     */
    public function __isset(string $field_name): bool
    {
        return isset($this->Fields[$field_name]);
    }

    /**
     * Field value mutator
     *
     * @param string $field_name
     * @param mixed $value
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return void
     */
    public function __set(string $field_name, $value): void
    {
        $this->setValue($field_name, $value);
    }

    /**
     * get a single field
     *
     * @param string $field_name
     * @return Nickwest\EloquentForms\Field
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getField(string $field_name): Field
    {
        if(!isset($this->Fields[$field_name])) {
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return $this->Fields[$field_name];
    }

     /**
     * get an array of all Fields
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->Fields;
    }

    /**
     * Add a single field to the form
     *
     * @param string $field_name
     * @return void
     */
    public function addField(string $field_name): void
    {
        $this->Fields[$field_name] = new Field($field_name);

        // Carry over the current theme to the Field
        $this->Fields[$field_name]->Theme = $this->Theme;
    }

    /**
     * Add a bunch of fields to the form, New fields will overwrite old ones with the same name
     *
     * @param array $field_names
     * @return void
     */
    public function addFields(array $field_names): void
    {
        foreach($field_names as $field_name) {
            $this->Fields[$field_name] = new Field($field_name);

            // Carry over the current theme to the Field
            $this->Fields[$field_name]->Theme = $this->Theme;
        }
    }

    /**
     * Remove a single field from the form if it exists
     *
     * @param string $field_name
     * @return void
     */
    public function removeField(string $field_name): void
    {
        if(isset($this->Fields[$field_name])) {
            unset($this->Fields[$field_name]);
        }
    }

    /**
     * Remove a bunch of fields to the form if they exist
     *
     * @param array $field_names
     * @return void
     */
    public function removeFields(array $field_names): void
    {
        foreach($field_names as $field_name) {
            if(isset($this->Fields[$field_name])) {
                unset($this->Fields[$field_name]);
            }
        }
    }

    /**
     * Is $field_name a field
     *
     * @param string $field_name
     * @return bool
     */
    public function isField(string $field_name): bool
    {
        return isset($this->Fields[$field_name]) && is_object($this->Fields[$field_name]);
    }

    /**
     * Add a Subform into the current form
     *
     * @param string $name
     * @param Nickwest\EloquentForms\Form $form
     * @param string $before_field
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function addSubform(string $name, Form $Form, string $before_field = ''): void
    {
        $this->addField($name);
        $this->Fields[$name]->Subform = $Form;

        // Insert it at a specific place in this form
        if($before_field != null) {
            $i = 0;
            foreach($this->display_fields as $key => $value) {
                if($value == $before_field) {
                    $this->display_fields = array_merge(array_slice($this->display_fields, 0, $i), array($name => $name), array_slice($this->display_fields, $i));
                    return;
                }
                $i++;
            }

            // If it wasn't found, then throw an exception
            throw new InvalidFieldException($before_field.' is not a display field');
        }

        // Stick it on the end of the form
        $this->display_fields[] = $name;
    }

    /**
     * Get an array of field values keyed by field name
     *
     * @return array
     */
    public function getFieldValues(): array
    {
        $values = [];

        foreach($this->Fields as $Field)
        {
            // Don't return subforms as fields they don't really have a valueaddDataList
            if(!$Field->isSubform()){
                $values[$Field->getOriginalName()] = $Field->attributes->value;
            }
        }

        return $values;
    }

    /**
     * Set a single field's value
     *
     * @param string $field_name
     * @param mixed $value
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setValue(string $field_name, $value): void
    {
        if(isset($this->Fields[$field_name])) {
            $this->Fields[$field_name]->attributes->value = $value;
        } else {
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }
    }

    /**
     * Get a single field's value
     *
     * @param string $field_name
     * @return mixed
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getValue(string $field_name)
    {
        if(!isset($this->Fields[$field_name])){
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return $this->Fields[$field_name]->attributes->value;
    }

    /**
     * Set multiple field values at once [field_name] => value
     *
     * @param array $values
     * @param bool $ignore_invalid
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setValues(array $values, bool $ignore_invalid = false): void
    {
        foreach($values as $field_name => $value) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->attributes->value = $value;

            } elseif(!$ignore_invalid) {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Set multiple field names at once [original_name] => new_name
     * Simple way to change all buttons to have the same name attribute in HTML
     *
     * @param array $names [original_name] => new_name
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setNames(array $names): void
    {
        foreach($names as $original_name => $name) {
            if(isset($this->Fields[$original_name])) {
                $this->Fields[$original_name]->attributes->name = $name;

            } else {
                throw new InvalidFieldException($original_name.' is not part of the Form');
            }
        }
    }

    /**
     * Set multiple field types at once [field_name] => type
     *
     * @param array $types
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidCustomFieldObjectException
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setTypes(array $types): void
    {
        foreach($types as $field_name => $type) {
            if(isset($this->Fields[$field_name])) {
                // If it's a custom type, it'll be an object
                if(is_object($type) && is_a($type, 'Nickwest\EloquentForms\CustomField')) {
                    $this->Fields[$field_name]->CustomField = $type;
                }
                // If it's some other object, it's not a valid type
                elseif(is_object($type)) {
                    throw new InvalidCustomFieldObjectException($field_name.' CustomField object need to extend Nickwest\EloquentForms\CustomField');
                }
                // It's probably just a string so set it
                else {
                    $this->Fields[$field_name]->attributes->type = $type;
                }
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Set multiple field examples at once [field_name] => value
     *
     * @param array $examples
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setExamples($examples): void
    {
        foreach($examples as $field_name => $example) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->example = $example;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Set multiple field default values at once [field_name] => value
     *
     * @param array $default_values
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setDefaultValues(array $default_values): void
    {
        foreach($default_values as $field_name => $default_value) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->default_value = $default_value;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Set multiple field required fields at oncel takes array of field names
     *
     * @param array $required_fields
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setRequiredFields(array $required_fields): void
    {
        //TODO: This should unset required from fields not in $required_fields
        foreach($required_fields as $field_name) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->attributes->required = true;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    // TODO: addRequiredFields(array)

    /**
     * set inline fields
     *
     * @param array $fields an array of field names
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setInline(array $fields): void
    {
        foreach($fields as $field_name) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->is_inline = true;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }


    /**
     * Add a data list to the form
     *
     * @param array $name
     * @param array $options
     * @return void
     */
    public function addDatalist(string $name, array $options)
    {
        $this->addField($name);

        $this->{$name}->attributes->type = 'datalist';
        $this->{$name}->attributes->id = $name;
        $this->{$name}->setOptions($options);

        $this->addDisplayFields([$name]);
    }

    /**
     * Set the array of fields to be displayed (order matters)
     *
     * @param array $field_names
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setDisplayFields(array $field_names): void
    {
        $fields = [];
        // TODO: add validation on field_names?
        foreach($field_names as $field) {
            if(!isset($this->Fields[$field])) {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
            $fields[$field] = $field;
        }

        $this->display_fields = $fields;
    }

    /**
     * Add multiple display fields field
     *
     * @param array $field_names
     * @return void
     */
    public function addDisplayFields(array $field_names): void
    {
        foreach($field_names as $field) {
            $this->display_fields[$field] = $field;
        }
    }

    /**
     * Remove multiple display fields field
     *
     * @param array $field_names
     * @return void
     */
    public function removeDisplayFields(array $field_names): void
    {
        foreach($field_names as $field) {
            if(isset($this->display_fields[$field])) {
                unset($this->display_fields[$field]);
            }
        }
    }

    /**
     * Add $display_field to the display array after $after_field
     *
     * @param string $display_field
     * @param string $after_field
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setDisplayAfter(string $display_field, string $after_field): void
    {
        $i = 0;
        foreach($this->display_fields as $key => $value) {
            if($value == $after_field) {
                $this->display_fields = array_merge(array_slice($this->display_fields, 0, $i+1), [$display_field => $display_field], array_slice($this->display_fields, $i+1));
                return;
            }
            $i++;
        }

        throw new InvalidFieldException($after_field.' is not part of the Form');
    }

    /**
     * Get an array of Display Field Names
     *
     * @return array
     */
    public function getDisplayFieldNames(): array
    {
        return $this->display_fields;
    }

    /**
     * Get an array of Field Objects (where those fields are set to display)
     *
     * @return array
     */
    public function getDisplayFields(): array
    {
        if(is_array($this->display_fields) && sizeof($this->display_fields) > 0) {
            $Fields = [];
            foreach($this->display_fields as $field_name) {
                $Fields[$field_name] = $this->Fields[$field_name];
            }
            return $Fields;
        }

        // TODO: should this return null instead? (Not if all fields are displayed with $this->display_fields is empty)
        return $this->Fields;
    }


    /**
     * Add field labels to the existing labels
     *
     * @param array $labels
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setLabels(array $labels): void
    {
        foreach($labels as $field_name => $label) {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->label = $label;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Get a list of all labels for the given $field_names, if $field_names is blank, get labels for all fields
     *
     * @param array $field_names
     * @return array
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getLabels(array $field_names = []): array
    {
        if(count($field_names) == 0) {
            $field_names = array_keys($this->Fields);
        }

        $labels = [];
        foreach($field_names as $field_name) {
            if(isset($this->Fields[$field_name])) {
                $labels[$field_name] = $this->Fields[$field_name]->label;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }

        return $labels;
    }

    /**
     * Set validation rules to Field(s).
     *
     * @param array $validation_rules [field_name] => rules
     * @return void
     */
    public function setValidationRules(array $validation_rules): void
    {
        foreach($validation_rules as $field_name => $rules)
        {
            if(isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->validation_rules = $rules;
            } else {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }

    /**
     * Get validation rules from Field(s).
     *
     * @return array $validation_rules
     */
    public function getValidationRules(): array
    {
        $validation_rules = [];
        foreach($this->Fields as $key => $Field){
            if($Field->validation_rules != ''){
                $validation_rules[$key] = $Field->validation_rules;
            }
        }

        return $validation_rules;
    }


    /**
     * Using validation rules, determine if form values are valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $rules = [];
        foreach($this->Fields as $Field) {
            $rules[$Field->getOriginalName()] = [];

            if(isset($Field->validation_rules)) {
                $rules[$Field->getOriginalName()] = $Field->validation_rules;
            }

            // Set required rule on all required fields
            if($Field->attributes->required && !in_array('required', $rules)) {
                if(!is_array($rules[$Field->getOriginalName()])) {
                    $rules[$Field->getOriginalName()] = [];
                }
                $rules[$Field->getOriginalName()][] = 'required';
            }

            // TODO: Could add more auto validation based on HTML field types (email, phone, etc)
        }

        // Set up the Validator
        $Validator = Validator::make(
            $this->getFieldValues(),
            $rules
        );

        // Set error messages to fields
        if(!($success = !$Validator->fails())) {
            foreach($Validator->errors()->toArray() as $field => $error) {
                $this->Fields[$field]->error_message = current($error);
            }
        }

        return $success;
    }

    /**
     * Add a submit button to the bottom of the form
     *
     * @param string $name
     * @param string $value
     * @param string $classes
     * @return void
     */
    public function addSubmitButton(string $name, string $value, string $classes=''): void
    {
        $this->SubmitFields[$name] = new Field($name);
        $this->SubmitFields[$name]->attributes->value = $value;
        $this->SubmitFields[$name]->label = $value;
        if($classes != ''){
            $this->SubmitFields[$name]->attributes->class = $classes;
        }
    }

    /**
     * Remove a submit button to the bottom of the form
     *
     * @param string $name
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function removeSubmitButton(string $name): void
    {
        if(!isset($this->SubmitFields[$name])){
            throw new InvalidFieldException($name.' is not part of the Form');
        }

        unset($this->SubmitFields[$name]);
    }

    /**
     * Get a submit button from the bottom of the form
     *
     * @param string $name
     * @return Nickwest\EloquentForms\Field
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getSubmitButton(string $name): Field
    {
        if(!isset($this->SubmitFields[$name])){
            throw new InvalidFieldException($name.' is not part of the Form');
        }

        return $this->SubmitFields[$name];
    }

    /**
     * Get all submit button Fields
     *
     * @return array
     */
    public function getSubmitButtons(): array
    {
        return $this->SubmitFields;
    }

     /**
     * Rename a submit button, and optionally also update its value
     *
     * @param string $name
     * @param string $new_name
     * @param string $new_value
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function renameSubmitButton(string $name, string $new_name, string $new_value=null): void
    {
        if(!isset($this->SubmitFields[$name])){
            throw new InvalidFieldException($name.' is not part of the Form');
        }

        if(isset($this->SubmitFields[$new_name])){
            throw new InvalidFieldException($new_name.' already exists');
        }

        $this->SubmitFields[$name]->attributes->name = $new_name;
        if($new_value !== null){
            $this->SubmitFields[$name]->attributes->value = $new_value;
        }

        $this->SubmitFields[$new_name] = $this->SubmitFields[$name];
        unset($this->SubmitFields[$name]);
    }

    /**
     * Set the theme
     *
     * @param Nickwest\EloquentForms\Theme $Theme
     * @return void
     */
    public function setTheme(Theme $Theme): void
    {
        $this->Theme = $Theme;
        foreach($this->Fields as $key => $Field) {
            $this->Fields[$key]->Theme = $Theme;
        }
    }

    /**
     * Get the theme
     *
     * @return Nickwest\EloquentForms\Theme $Theme
     */
    public function getTheme(): Theme
    {
        return $this->Theme;
    }


    /**
     * Make a view and extend $extends in section $section, $blade_data is the data array to pass to View::make()
     *
     * @param array $blade_data
     * @param string $extends
     * @param string $section
     * @param bool $view_only
     * @return Illuminate\View\View
     */
    public function makeView(array $blade_data = [], string $extends = '', string $section = '', bool $view_only = false): \Illuminate\View\View
    {
        $blade_data['Form'] = $this;
        $blade_data['extends'] = $extends;
        $blade_data['section'] = $section;
        $blade_data['view_only'] = $view_only;

        // Check if this form should be multipart
        foreach($this->Fields as $Field){
            if(isset($this->attributes->enctype)){
                break;
            }

            if($Field->attributes->type == 'file'){
                $this->attributes->enctype = 'multipart/form-data';
            }elseif($Field->isSubform()){
                foreach($Field->Subform->Fields as $SubField){
                    if($SubField->attributes->type == 'file'){
                        $this->attributes->enctype = 'multipart/form-data';
                    }
                }
            }
        }

        $this->Theme->prepareFormView($this);

        if($extends != '') {
            // If the custom Theme doesn't have an override use the default instead.
            if($this->Theme->getViewNamespace() != '' && View::exists($this->Theme->getViewNamespace().'::form-extend')) {
                return View::make($this->Theme->getViewNamespace().'::form-extend', $blade_data);
            }
            return View::make(DefaultTheme::getDefaultNamespace().'::form-extend', $blade_data);
        }
        if($this->Theme->getViewNamespace() != '' && View::exists($this->Theme->getViewNamespace().'::form')) {
            return View::make($this->Theme->getViewNamespace().'::form', $blade_data);
        }
        return View::make(DefaultTheme::getDefaultNamespace().'::form', $blade_data);
    }

    /**
     * Make a view, $blade_data is the data array to pass to View::make()
     *
     * @param array $blade_data
     * @param bool $view_only
     * @return View
     */
    public function makeSubformView(array $blade_data, bool $view_only = false)
    {
        $blade_data['Form'] = $this;
        $blade_data['view_only'] = $view_only;

        if($this->Theme->getViewNamespace() != '' && View::exists($this->Theme->getViewNamespace().'::subform')) {
            return View::make($this->Theme->getViewNamespace().'::subform', $blade_data);
        }
        return View::make(DefaultTheme::getDefaultNamespace().'::subform', $blade_data);
    }

    /**
     * Get a JSON representation of this Form
     *
     * @return string JSON
     */
    public function toJson()
    {
        $array = [
            'laravel_csrf' => $this->laravel_csrf,
            'attributes' => json_decode($this->attributes->toJson()),
            'Fields' => [],
            'display_fields' => $this->display_fields,
            'Theme' => (is_object($this->Theme) ? '\\'.get_class($this->Theme) : null),
        ];

        foreach($this->Fields as $key => $Field) {
            $array['Fields'][$key] = json_decode($Field->toJson());
        }

        return json_encode($array);
    }

    /**
     * Make A Form from JSON
     *
     * @param string $json
     * @return Nickwest\EloquentForms\Form
     */
    public function fromJson(string $json): Form
    {
        $array = json_decode($json);

        foreach($array as $key => $value) {
            if($key == 'Fields') {
                foreach($value as $key2 => $array) {
                    $this->$key[$key2] = new Field($key2);
                    $this->$key[$key2]->fromJson(json_encode($array));
                }

            } elseif($key == 'attributes') {
                $this->attributes = new Attributes();
                $this->attributes->fromJson(json_encode($value));

            } elseif($key == 'Theme' && $value != null) {
                $this->Theme = new $value(); // TODO: make a to/from JSON method on this? is it necessary?

            } elseif(is_object($value)) {
                $this->$key = (array)$value;

            } else {
                $this->$key = $value;
            }
        }

        return $this;
    }


}
