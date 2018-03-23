<?php

namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Nickwest\EloquentForms\Exceptions\NotImplementedException;
use Nickwest\EloquentForms\Traits\DataFromMySQL;

trait FormTrait
{
    // Added functionality for pulling extra column data from MySQL
    use DataFromMySQL;

    /**
     * Form object see Nickwest\EloquentForms\Form.
     *
     * @var Form
     */
    protected $Form = null;

    /**
     * @var array
     */
    protected $valid_columns = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $blank_select_text = '-- Select One --';

     /**
     * @var string
     */
    protected $multi_delimiter = '|';

    /**
     * @var bool
     */
    public $validate_on_save = true;

    /**
     * Boot the trait. Adds an observer class for form.
     *
     * @return void
     */
    public static function bootFormTrait()
    {
        static::observe(new FormObserver);
    }

    /**
     * Constructor
     *
     * @return mixed
     */
    public function __construct(array $attributes = [], bool $validate_on_save = true)
    {
        $this->validate_on_save = $validate_on_save;

        return parent::__construct($attributes);
    }

    /**
     * Access for validate_on_save.
     *
     * @return bool
     */
    public function validateOnSave()
    {
        return $this->validate_on_save;
    }

    /**
     * Get the form object
     *
     * @return Form
     */
    public function Form(): Form
    {
        if (! is_object($this->Form)) {
            $this->Form = new Form();
        }

        return $this->Form;
    }

    /**
     * return an array of columns.
     *
     * @return array
     */
    public function getColumnsArray(): array
    {
        return $this->getAllColumns();
    }

    /**
     * Make a View for the form and return the rendered output.
     *
     * @param array $blade_data
     * @param string $extends
     * @param string $section
     * @param bool $view_only
     * @return View
     */
    public function getFormView(array $blade_data, string $extends = '', string $section = '', bool $view_only = false): \Illuminate\View\View
    {
        return $this->Form()->makeView($blade_data, $extends, $section, $view_only);
    }

    /**
     * Make a View for the field and return the rendered output.
     *
     * @param string $field_name
     * @param array $options
     * @return View
     */
    public function getFieldView(string $field_name): \Illuminate\View\View
    {
        return $this->Form()->$field_name->makeView();
    }

    /**
     * Make a View for the field and return the rendered output.
     *
     * @param string $field_name
     * @param array $options
     * @return View
     */
    public function getFieldDisplayView(string $field_name, array $options = []): \Illuminate\View\View
    {
        return $this->Form()->$field_name->makeDisplayView();
    }

    /**
     * String to append to each label.
     *
     * @param string $suffix
     * @return void
     */
    public function setLabelSuffix(string $suffix)
    {
        foreach ($this->Form()->getFields() as $Field) {
            $Field->label_suffix = $suffix;
        }
    }

    /**
     * Set the values from a post data array to $this model,
     * returned bool indicates if anything changed.
     *
     * @param array $post_data
     * @return void
     */
    public function setPostValues(array $post_data): void
    {
        foreach ($post_data as $field_name => $value) {
            // We don't throw Exceptions here because post fields come from users and could be anything
            // We just silently ignore invalid post fields
            if ($this->isColumn($field_name) && $this->isFillable($field_name)) { //TODO: should we limit to only display fields here too?
                if (is_object($this->Form()->{$field_name}->CustomField)) {
                    try {
                        $value = $this->Form()->{$field_name}->CustomField->hook_setPostValues($value);
                    } catch (NotImplementedException $e) {
                    }
                }

                $this->Form()->{$field_name}->attributes->value = $value;
                $this->{$field_name} = (is_array($value) ? implode($this->multi_delimiter, $value) : $value);
            }
        }

        // Make sure no Form fields were omitted from the post array (checkboxes can be when none are set)
        foreach ($this->Form()->getDisplayFields() as $Field) {
            if (isset($post_data[$Field->attributes->name]) || ! $this->isFillable($Field->getOriginalName())) {
                continue;
            }

            // If they were omitted set it to null
            if ($this->Form()->{$Field->getOriginalName()}->attributes->value != '') {
                $this->Form()->{$Field->getOriginalName()}->attributes->value = null;
                $this->{$Field->getOriginalName()} = null;
            }
        }
    }

    /**
     * Set all of the form values to whatever the value on that attribute of the model is.
     *
     * @return void
     */
    public function setAllFormValues(): void
    {
        foreach ($this->Form()->getFields() as $Field) {
            // Use the Model Field's value if it has the field
            if (isset($this->{$Field->getOriginalName()})) {
                $value = $this->{$Field->getOriginalName()};
            }
            // Use the Field's Default value if it has one
            elseif ($this->Form()->{$Field->getOriginalName()}->default_value != null) {
                $value = $this->Form()->{$Field->getOriginalName()}->default_value;
            }
            // Just use null
            else {
                $value = null;
            }

            // If the field has a CustomField object, then try to use it's hook for setting them value.
            if (is_object($Field->CustomField)) {
                try {
                    $this->Form()->{$Field->getOriginalName()}->attributes->value = $Field->CustomField->hook_setAllFormValues($Field, $value);
                    continue;
                } catch (NotImplementedException $e) {
                    dump('caught');
                }
            }

            // If it's a checkbox or otherwise has multi_key set, assume we have a divided string that needs to be made into an array.
            if ($value !== null && ! is_array($value) && ($Field->attributes->type == 'checkbox' || $Field->attributes->multi_key)) {
                $value = explode($this->multi_delimiter, $value);
            }

            $this->Form()->{$Field->getOriginalName()}->attributes->value = $value;
        }
    }

    /**
     * Create the Form object from Json.
     *
     * @param string $json
     * @return void
     */
    public function generateFormFromJson(string $json): void
    {
        $this->Form = new Form();
        $this->Form->fromJson($json);
    }

    /**
     * Determine if $field_name is a Column in the table this model models.
     *
     * @param string $field_name
     * @return bool
     */
    public function isColumn(string $field_name): bool
    {
        if (count($this->valid_columns) <= 0) {
            $this->getAllColumns();
        }

        if (isset($this->valid_columns[$field_name])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of model, based on field requirements & table structure & extra rules.
     *
     * TODO: this could be re-written to use Form::isValid with extra rules injected and values set from Model
     *
     * @var bool
     */
    public function isFormValid(): bool
    {
        // Add required fields to field_rules
        $columns = $this->getAllColumns();

        $rules = [];
        foreach ($this->Form()->getFields() as $Field) {
            if (is_array($Field->validation_rules)) {
                $rules[$Field->getOriginalName()] = $Field->validation_rules;
            } else {
                $rules[$Field->getOriginalName()] = explode($this->multi_delimiter, $Field->validation_rules);
            }

            // We need to do this here because we're not using Form::isValid() we're using the values on the model itself
            // And validating against those using the Form rules + some extra from the table if possible
            if ($Field->attributes->required && ! in_array('required', $rules)) {
                $rules[$Field->getOriginalName()][] = 'required';
            }

            if (isset($columns[$Field->getOriginalName()])) {
                $this->addMaxRule($columns[$Field->getOriginalName()], $rules[$Field->getOriginalName()], $Field);
            }
        }

        // Set up the Validator
        $Validator = Validator::make(
            $this->getAttributes(),
            $rules
        );

        // Set error messages to fields
        if (! ($success = ! $Validator->fails())) {
            foreach ($Validator->errors()->toArray() as $field => $error) {
                $this->Form()->$field->error_message = current($error);
            }
        }

        return $success;
    }

    /**
     * Add or adjust max length rule if column exists and has a max length.
     */
    protected function addMaxRule($column, array &$rules)
    {
        // Set max length rule based on column length if available
        if (isset($column['length']) && $column['length'] != '') {
            // Find existing max: rules
            $max_rules = preg_grep('/^max:/', $rules);

            foreach ($max_rules as $key => $rule) {
                if ((int) substr($rule, 4) > $column['length']) {
                    $rules[$key] = 'max:'.$column['length'];
                }
            }

            if (count($max_rules) == 0) {
                $rules[] = 'max:'.$column['length'];
            }
        }
    }

    /**
     * Get a list of form data to build a form.
     *
     * @return void
     */
    protected function generateFormData(): void
    {
        $columns = $this->getAllColumns();

        foreach ($columns as $column) {
            $this->Form()->addField($column['name']);
            $this->Form()->{$column['name']}->attributes->maxlength = $column['length'];
            $this->Form()->{$column['name']}->default_value = $column['default'];
            $this->Form()->{$column['name']}->attributes->type = $this->getFormTypeFromColumnType($column['type']);
            if (is_array($column['values'])) {
                $this->Form()->{$column['name']}->options->setOptions($column['values']);
            }
            $this->Form()->addDisplayFields([$column['name']]);
        }
    }

    /**
     * Get a list of all valid columns on the model using this trait.
     *
     * @return array
     */
    protected function getAllColumns(): array
    {
        if (count($this->columns) > 0) {
            return $this->columns;
        }

        // If we have a MySQL Driver, then query directly to get Enum option values
        if (DB::connection()->getDriverName() == 'mysql') {
            $this->setColumnsFromMySQL();
        }
        // Otherwise query through Doctrine so we can get something still.
        else {
            $this->setColumnsFromOther();
        }

        return $this->columns;
    }

    /**
     * Set columns using data we can get through Doctrine
     */
    private function setColumnsFromOther(): void
    {
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing($this->table);

        foreach ($columns as $column_name) {
            $DoctrineColumn = DB::connection()->getDoctrineColumn($this->getTable(), $column_name);

            $this->columns[$column_name] = [
                'name' => $column_name,
                'type' => $DoctrineColumn->getType()->getName(),
                'default' => $DoctrineColumn->getDefault(),
                'length' => $DoctrineColumn->getLength(),
                'values' => null,
            ];
            $this->valid_columns[$column_name] = $column_name;
        }
    }

    /**
     * Get the field type based on column type.
     *
     * @param string $type
     * @return string
     */
    private function getFormTypeFromColumnType(string $type): string
    {
        switch ($type) {
            // TODO: Expand on this with more HTML5 field types
            case 'enum':
                return 'select';

            case 'text':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
                return 'textarea';

            default:
                return 'text';
        }
    }
}
