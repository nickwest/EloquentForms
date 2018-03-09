<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

use Nickwest\EloquentForms\Form;

trait FormTrait{

    /**
     * Form object see Nickwest\EloquentForms\Form
     *
     * @var Form
     */
    protected $Form = null;

    protected $valid_columns = [];
    protected $columns = [];

    protected $blank_select_text = '-- Select One --';
    protected $label_postfix = '';

    protected $multi_delimiter = '|';

    protected $validation_rules = [];

    protected $validate_on_save = true;

    /**
     * Boot the trait. Adds an observer class for form
     *
     * @return void
     */
    public static function bootFormTrait()
    {
        // function save() method is hooked by FormObserver and runs validation
        if($this->validate_on_save){
            static::observe(new FormObserver);
        }
    }

    public function __construct(bool $validate_on_save = true)
    {
        $this->validate_on_save = $validate_on_save;
    }

    /**
     * Boot the trait. Adds an observer class for form
     *
     * @return Form
     */
    public function Form(): Form
    {
        if(!is_object($this->Form)) {
            $this->Form = new Form();
        }

        return $this->Form;
    }

    /**
     * return an array of columns
     *
     * @return array
     */
    public function getColumnsArray(): array
    {
        return $this->getAllColumns();
    }

    /**
     * Make a View for the form and return the rendered output
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
     * Make a View for the field and return the rendered output
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
     * Make a View for the field and return the rendered output
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
     * Set the values from a post data array to $this model,
     * returned bool indicates if anything changed
     *
     * @param array $post_data
     * @return void
     */
    public function setPostValues(array $post_data): void
    {
        foreach($post_data as $field_name => $value) {
            // We don't throw Exceptions here because post fields come from users and could be anything
            // We just silently ignore invalid post fields
            if($this->isColumn($field_name) && $this->isFillable($field_name)) { //TODO: should we limit to only display fields here too?
                if(is_object($this->Form()->{$field_name}->CustomField)) {
                    try {
                        $value = $this->Form()->{$field_name}->CustomField->hook_setPostValues($value);
                    }
                    catch(NotImplementedException $e){}
                }

                $this->Form()->{$field_name}->Attributes->value = $value;
                if(is_array($value)) {
                    $this->{$field_name} = implode($this->multi_delimiter, $value);
                } else {
                    $this->{$field_name} = $value;
                }
            }
        }

        // Make sure no Form fields were omitted from the post array (checkboxes can be when none are set)
        foreach($this->Form()->getDisplayFields() as $Field) {
            if(isset($post_data[$Field->Attributes->name]) || !$this->isFillable($Field->getOriginalName())) {
                continue;
            }

            // If they were omitted set it to null
            if($this->Form()->{$Field->getOriginalName()} != '') {
                $this->Form()->{$Field->getOriginalName()} = null;
                $this->{$Field->getOriginalName()} = null;
            }
        }
    }

    /**
     * Validation of form, based on requirements & extra rules
     *
     * @var bool
     */
    public function formIsValid(): bool
    {
        $this->Form()->setValidationRules($this->validation_rules);

        return $this->Form()->isValid();
    }

    /**
     * Set all of the form values to whatever the value on that attribute of the model is
     *
     * @return void
     */
    public function setAllFormValues(): void
    {
        foreach($this->Form()->getFields() as $Field) {
            if(is_object($Field->CustomField)) {
                try {
                    // If the model doesn't have this field, then use default or empty string as the starting value
                    if(isset($this->{$Field->getOriginalName()})){
                        $this->Form()->{$Field->getOriginalName()}->value = $this->{$Field->getOriginalName()};
                    }else{
                        $this->Form()->{$Field->getOriginalName()}->value = $this->Form()->{$Field->getOriginalName()}->default_value != '' ? $this->Form()->{$Field->getOriginalName()}->default_value : '';
                    }
                    continue; // Next field
                }
                catch(NotImplementedException $e){}
            }

            if($Field->Attributes->type == 'checkbox' || $Field->Attributes->multi_key) {
                if((!isset($this->{$Field->getOriginalName()}) || ($this->{$Field->getOriginalName()} == '' && $this->{$Field->getOriginalName()} !== 0)) && $this->Form()->{$Field->getOriginalName()}->default_value != '') {
                    $this->Form()->{$Field->getOriginalName()}->Attributes->value = $this->Form()->{$Field->getOriginalName()}->default_value;
                } else {
                    if(!is_array($this->{$Field->getOriginalName()})){
                        $values = array();
                        foreach(explode($this->multi_delimiter, $this->{$Field->getOriginalName()}) as $value) {
                            $values[$value] = $value;
                        }
                    }else{
                        $values = $this->{$Field->getOriginalName()}->Attributes->value;
                    }

                    $this->Form()->{$Field->getOriginalName()}->Attributes->value = $values;
                }
            } else {
                // If the model doesn't have this field, then use default or empty string as the starting value
                if(isset($this->{$Field->getOriginalName()})){
                    $this->Form()->{$Field->getOriginalName()}->Attributes->value = $this->{$Field->getOriginalName()};
                } else {
                    $this->Form()->{$Field->getOriginalName()}->Attributes->value = $this->Form()->{$Field->getOriginalName()}->default_value != null ? $this->Form()->{$Field->getOriginalName()}->default_value : '';
                }
            }
        }
    }

    /**
     * Create the Form object from Json
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
     * Determine if $field_name is a Column in the table this model models
     *
     * @param string $field_name
     * @return bool
     */
    public function isColumn(string $field_name): bool
    {
        if(sizeof($this->valid_columns) <= 0) {
            $this->getAllColumns();
        }

        if(isset($this->valid_columns[$field_name])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of model, based on field requirements & table structure & extra rules
     *
     * @var bool
     */
    protected function isValid(): bool
    {
        // Add required fields to field_rules
        $columns = $this->getAllColumns();
        $rules = [];
        foreach($this->Form()->getFields() as $Field) {
            $rules[$Field->getOriginalName()] = [];

            if(isset($this->validation_rules[$Field->getOriginalName()]) && $this->validation_rules[$Field->getOriginalName()] != '') {
                $rules[$Field->getOriginalName()] = explode('|', $this->validation_rules[$Field->getOriginalName()]);
            }

            if($Field->Attributes->required && !in_array('required', $rules)) {
                $rules[$Field->getOriginalName()][] = 'required';
            }

            if(isset($columns[$Field->getOriginalName()]) && isset($columns[$Field->getOriginalName()]['length'])) {
                $found = false;
                foreach($rules[$Field->getOriginalName()] as $key => $rule) {
                    if(strpos($rule, 'max') === 0) {
                        $found = true;
                        $max = (int)substr($rule, 4);
                        if($max > $columns[$Field->getOriginalName()]['length']) {
                            $rules[$Field->getOriginalName()][$key] = 'max:'.$columns[$Field->getOriginalName()]['length'];
                        }
                        break;
                    }
                }

                if(!$found) {
                    $rules[] = 'max:'.$columns[$Field->getOriginalName()]['length'];
                }
            }

        }

        // Set up the Validator
        $Validator = Validator::make(
            $this->getAttributes(),
            $rules
        );

        // Set error messages to fields
        if(!($success = !$Validator->fails())) {
            foreach($Validator->errors()->toArray() as $field => $error) {
                $this->Form()->$field->error_message = current($error);
            }
        }

        return $success;
    }

    /**
     * Get a list of form data to build a form
     *
     * @return void
     */
    protected function generateFormData(): void
    {
        $columns = $this->getAllColumns();

        foreach($columns as $column) {
            $this->Form()->addField($column['name']);
            $this->Form()->{$column['name']}->Attributes->maxlength = $column['length'];
            $this->Form()->{$column['name']}->default_value = $column['default'];
            $this->Form()->{$column['name']}->Attributes->type = $this->getFormTypeFromColumnType($column['type']);
            if(is_array($column['values'])){
                $this->Form()->{$column['name']}->setOptions($column['values']);
            }
            $this->Form()->addDisplayFields([$column['name']]);
        }
    }

    /**
     * Get a list of all valid columns on the model using this trait
     *
     * @return array
     */
    protected function getAllColumns(): array
    {
        if(count($this->columns) > 0) {
            return $this->columns;
        }

        // If we have a MySQL Driver, then query directly to get Enum option values
        if(DB::connection()->getDriverName() == 'mysql'){
            $query = 'SHOW COLUMNS FROM '.$this->getTable();

            foreach(DB::connection($this->connection)->select($query) as $column) {
                $this->columns[$column->Field] = [
                    'name' => $column->Field,
                    'type' => $this->getSQLType($column->Type),
                    'default' => $column->Default,
                    'length' => $this->getSQLLength($column->Type),
                    'values' => $this->getSQLEnumOptions($column->Type, $column->Null == 'YES')
                ];
                $this->valid_columns[$column->Field] = $column->Field;
            }
        }
        // Otherwise query through Doctrine so we can get something still.
        else{
            $columns = DB::connection()->getSchemaBuilder()->getColumnListing($this->table);

            foreach($columns as $column_name){
                $DoctrineColumn = DB::connection()->getDoctrineColumn($this->getTable(), $column_name);
                //dump($DoctrineColumn->getColumnDefinition());
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
        //dd($this->columns);


        return $this->columns;
    }

    /**
     * Isolate and return the column type
     *
     * @param string $type
     * @return string
     */
    private function getSQLType(string $type): string
    {
        $types = array(
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint',
            'decimal', 'float', 'double', 'real',
            'bit', 'boolean', 'serial',
            'date', 'datetime', 'timestamp', 'time', 'year',
            'char', 'varchar',
            'tinytext', 'text', 'mediumtext', 'longtext',
            'binary', 'varbinary',
            'tinyblob', 'mediumblob', 'blob', 'longblob',
            'enum', 'set',
        );

        foreach($types as $key) {
            if(strpos($type, $key) === 0) {
                return $key;
            }
        }
    }

    // private function getDoctrineType($type)
    // {
    //     $types = array(

    //     )
    // }

    /**
     * Isolate and return the column length
     *
     * @param string $type
     * @return mixed
     */
    private function getSQLLength(string $type)
    {
        if(strpos($type, 'enum') === 0) {
            return;
        }

        if(strpos($type, '(') !== false) {
            return substr($type, strpos($type, '(')+1, strpos($type, ')') - strpos($type, '(')-1);
        }

        $lengths = array(
            'tinytext' => 255,
            'text' => 65535,
            'mediumtext' => 1677215,
            'longtext' => 4294967295,

        );

        foreach($lengths as $key => $length) {
            if(strpos($type, $key) === 0) {
                return $length;
            }
        }
    }

    /**
     * Isolate and return the values for enums
     *
     * @param string $type
     * @param bool $nullable
     * @return mixed
     */
    private function getSQLEnumOptions(string $type, bool $nullable=false)
    {
        if(strpos($type, 'enum') !== 0) {
            return;
        }
        $values = explode(',', str_replace("'", '', substr($type, strpos($type, '(')+1, strpos($type, ')') - strpos($type, '(')-1)));

        $return_array = null;

        foreach($values as $value) {
            if($value == '') {
                $return_array[$value] = $this->blank_select_text;
            } else {
                $return_array[$value] = $value;
            }
        }

        if(!isset($return_array['']) && $nullable) {
            $return_array = array_merge(['' => $this->blank_select_text], $return_array);
        }

        return $return_array;
    }

    /**
     * Get the field type based on column type
     *
     * @param string $type
     * @return string
     */
    private function getFormTypeFromColumnType(string $type): string
    {
        switch($type) {
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
