<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use Nickwest\EloquentForms\Exceptions\InvalidFieldException;

class Form{

    /**
     * Submit Button value (used on single submit button only)
     *
     * @var string
     */
    public $submit_button_value = null;

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
    public $Attributes = null;

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
     * Array of field_names to display
     *
     * @var array
     */
    protected $submit_buttons = [];

    /**
     * Array of valid columns for the model using this trait
     *
     * @var array
     */
    protected $valid_columns = [];

    /**
     * Add Delete button?
     *
     * @var bool
     */
    protected $allow_delete = false;

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
        $this->Attributes = new Attributes;

        // Set the action to default to the current path
        $this->Attributes->action = Request::url();
    }

    /**
     * Field value accessor
     *
     * @param string $field_name
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return mixed
     */
    public function __get(string $field_name)
    {
        if(!isset($this->Fields[$field_name])){
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return $this->Fields[$field_name];
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
        if(!isset($this->Fields[$field_name])){
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return isset($this->Fields[$field_name]);
    }

    /**
     * Field value mutator
     *
     * @param string $key
     * @param mixed $value
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return void
     */
    public function __set(string $field_name, $value)
    {
        if(!isset($this->Fields[$field_name])){
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        if(isset($this->Fields[$field_name])) {
            $this->Fields[$field_name]->value = $value;
        }
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
     * Add a single field to the form
     *
     * @param string $field_name
     * @return void
     */
    public function addField(string $field_name)
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
    public function addFields(array $field_names)
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
    public function removeField(string $field_name)
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
    public function removeFields(array $field_names)
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
     * Get an array of field values keyed by field name
     *
     * @return array
     */
    public function getFieldValues(): array
    {
        $values = [];

        foreach($this->Fields as $Field)
        {
            $values[$Field->name] = $Field->value;
        }

        return $values;
    }

    /**
     * Set the array of fields to be displayed (order matters)
     *
     * @param array $field_names
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setDisplayFields(array $field_names)
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
    public function addDisplayFields(array $field_names)
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
    public function removeDisplayFields(array $field_names)
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
    public function setDisplayAfter(string $display_field, string $after_field)
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
     * Remove a single field from the form
     *
     * @param array $field_name
     * @return array
     */
    public function getDisplayFields()
    {
        return $this->display_fields;

        if(is_array($this->display_fields) && sizeof($this->display_fields) > 0) {
            $Fields = [];
            foreach($this->display_fields as $field_name) {
                $Fields[$field_name] = $this->{$field_name};
            }

            return $Fields;
        }

        return $this->Fields;
    }

    /**
     * Add field labels to the existing labels
     *
     * @param array $labels
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setLabels(array $labels)
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
    public function getLabels(array $field_names = [])
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
     * Set the theme
     *
     * @param \Nickwest\EloquentForms\Theme $Theme
     * @return void
     */
    public function setTheme(\Nickwest\EloquentForms\Theme &$Theme)
    {
        $this->Theme = $Theme;
        foreach($this->Fields as $key => $Field) {
            $this->Fields[$key]->Theme = $Theme;
        }
    }

    /**
     * Get the theme
     *
     * @return \Nickwest\EloquentForms\Theme $Theme
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
     * @return View
     */
    public function makeView(array $blade_data = [], string $extends = '', string $section = '', bool $view_only = false)
    {
        $blade_data['Form'] = $this;
        $blade_data['extends'] = $extends;
        $blade_data['section'] = $section;
        $blade_data['view_only'] = $view_only;

        // Check if this form should be multipart
        foreach($this->Fields as $Field){
            if(isset($this->Attributes->enctype)){
                break;
            }

            if($Field->attributes->type == 'file'){
                $this->Attributes->enctype = 'multipart/form-data';
            }elseif($Field->is_subform){
                foreach($Field->subform->Fields as $SubField){
                    if($SubField->attributes->type == 'file'){
                        $this->Attributes->enctype = 'multipart/form-data';
                    }
                }
            }
        }

        if($extends != '') {
            if($this->Theme->view_namespace != '' && View::exists($this->Theme->view_namespace.'::form-extend')) {
                return View::make($this->Theme->view_namespace.'::form-extend', $blade_data);
            }
            return View::make('form-maker::form-extend', $blade_data);
        }
        if($this->Theme->view_namespace != '' && View::exists($this->Theme->view_namespace.'::form')) {
            return View::make($this->Theme->view_namespace.'::form', $blade_data);
        }
        return View::make('form-maker::form', $blade_data);
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

        // foreach($this->display_fields as $field)
        // {
        //     $this->Fields[$field]->setupAttributes();
        // }

        if($this->Theme->view_namespace != '' && View::exists($this->Theme->view_namespace.'::subform')) {
            return View::make($this->Theme->view_namespace.'::subform', $blade_data);
        }
        return View::make('form-maker::subform', $blade_data);
    }
}
