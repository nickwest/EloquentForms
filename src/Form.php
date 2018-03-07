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

        return $this->Fields[$field_name]->value;
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

        return isset($this->Fields[$field_name]->value);
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

}
