<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;

trait FormTrait{

    /**
     * Form object see Nickwest\EloquentForms\Form
     *
     * @var Form
     */
    protected $Form = null;

    protected $valid_columns = array();
    protected $columns = array();

    protected $blank_select_text = '-- Select One --';
    protected $label_postfix = '';

    protected $multi_delimiter = '|';

    protected $validation_rules = [];


    /**
     * Boot the trait. Adds an observer class for form
     *
     * @return void
     */
    public static function bootFormTrait()
    {
        // function save() method is hooked by FormObserver and runs validation
        static::observe(new FormObserver);
    }

    /**
     * Boot the trait. Adds an observer class for form
     *
     * @return Form
     */
    public function Form()
    {
        if(!is_object($this->Form)) {
            $this->Form = new Form();
        }

        return $this->Form;
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
    public function getFormView(array $blade_data, string $extends = '', string $section = '', bool $view_only = false)
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
    public function getFieldView(string $field_name, $options=array())
    {
        return $this->Form()->$field_name->makeView($options);
    }

    /**
     * Make a View for the field and return the rendered output
     *
     * @param string $field_name
     * @param array $options
     * @return View
     */
    public function getFieldDisplayView($field_name, $options=array())
    {
        return $this->Form()->$field_name->makeDisplayView($options);
    }


    /**
     * Set the values from a post data array to $this model,
     * returned bool indicates if anything changed
     *
     * @param array $post_data
     * @return void
     */
    public function setPostValues($post_data){
        foreach($post_data as $field_name => $value) {
            if($this->isColumn($field_name) && $this->isFillable($field_name)) {
                if(is_object($this->Form()->{$field_name}->CustomField)) {
                    try {
                        $value = $this->Form()->{$field_name}->CustomField->hook_setPostValues($value);
                    }
                    catch(NotImplementedException $e){}
                }

                $this->Form()->{$field_name} = $value;
                if(is_array($value)) {
                    $this->{$field_name} = implode($this->multi_delimiter, $value);
                } else {
                    $this->{$field_name} = $value;
                }
            }
        }

        // Make sure no Form fields were omitted from the post array (checkboxes can be when none are set)
        foreach($this->Form()->getDisplayFields() as $Field) {
            if(isset($post_data[$Field->name]) || !$this->isFillable($Field->original_name)) {
                continue;
            }

            // If they were omitted set it to null
            if($this->Form()->{$Field->original_name} != '') {
                $this->Form()->{$Field->original_name} = null;
                $this->{$Field->original_name} = null;
            }
        }
    }

    /**
     * Validation of form, based on requirements & extra rules
     *
     * @var bool
     */
    public function formIsValid()
    {
        $this->Form()->setValidationRules($this->validation_rules);

        return $this->Form()->isValid();
    }

    /**
     * Validation of model, based on field requirements & table structure & extra rules
     *
     * @var bool
     */
    public function isValid()
    {
        // Add required fields to field_rules
        $columns = $this->getAllColumns();
        $rules = [];
        foreach($this->Form()->getFields() as $Field) {
            $rules[$Field->original_name] = [];

            if(isset($this->validation_rules[$Field->original_name]) && $this->validation_rules[$Field->original_name] != '') {
                $rules[$Field->original_name] = explode('|', $this->validation_rules[$Field->original_name]);
            }

            if($Field->attributes->required && !in_array('required', $rules)) {
                $rules[$Field->original_name][] = 'required';
            }

            if(isset($columns[$Field->original_name]) && isset($columns[$Field->original_name]['length'])) {
                $found = false;
                foreach($rules[$Field->original_name] as $key => $rule) {
                    if(strpos($rule, 'max') === 0) {
                        $found = true;
                        $max = (int)substr($rule, 4);
                        if($max > $columns[$Field->original_name]['length']) {
                            $rules[$Field->original_name][$key] = 'max:'.$columns[$Field->original_name]['length'];
                        }
                        break;
                    }
                }

                if(!$found) {
                    $rules[] = 'max:'.$columns[$Field->original_name]['length'];
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
     * Set all of the form values to whatever the value on that attribute of the model is
     *
     * @return void
     */
    public function setAllFormValues()
    {
        foreach($this->Form()->getFields() as $Field) {
            if(is_object($Field->CustomField)) {
                try {
                    // This is so bad... I'm sorry.
                    $this->Form()->{$Field->original_name}->value =
                        $Field->CustomField->hook_setAllFormValues($Field, (
                            isset($this->{$Field->original_name})
                            ? $this->{$Field->original_name}
                            : (
                                $this->Form()->{$Field->original_name}->default_value != ''
                                ? $this->Form()->{$Field->original_name}->default_value
                                : ''
                            )
                        )
                    );
                    continue;
                }
                catch(NotImplementedException $e){}
            }

            if($Field->type == 'checkbox' || $Field->multiple) {
                if((!isset($this->{$Field->original_name}) || ($this->{$Field->original_name} == '' && $this->{$Field->original_name} !== 0)) && $this->Form()->{$Field->original_name}->default_value != '') {
                    $this->Form()->{$Field->original_name}->value = $this->Form()->{$Field->original_name}->default_value;
                } else {
                    if(!is_array($this->{$Field->original_name})){
                        $values = array();
                        foreach(explode($this->multi_delimiter, $this->{$Field->original_name}) as $value) {
                            $values[$value] = $value;
                        }
                    }else{
                        $values = $this->{$Field->original_name};
                    }

                    $this->Form()->{$Field->original_name} = $values;
                }
            } else {
                $this->Form()->{$Field->original_name} =
                (
                    isset($this->{$Field->original_name})
                    ? $this->{$Field->original_name}
                    : (
                        $this->Form()->{$Field->original_name}->default_value != ''
                        ? $this->Form()->{$Field->original_name}->default_value
                        : ''
                    )
                );
            }
        }
    }

    /**
     * Create teh Form object from  Json
     *
     * @param string $json
     * @return void
     */
    public function generateFormFromJson(string $json)
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
    public function isColumn($field_name)
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
     * Get a list of form data to build a form
     *
     * @return void
     */
    protected function generateFormData()
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
    protected function getAllColumns()
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
                    'length' => $this->getLength($column->Type),
                    'values' => $this->getEnumOptions($column->Type, $column->Null == 'YES')
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
    private function getSQLType($type)
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
     * @return int
     */
    private function getLength($type)
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
     * @return array
     */
    private function getEnumOptions($type, $nullable=false)
    {
        if(strpos($type, 'enum') !== 0) {
            return;
        }
        $values = explode(',', str_replace("'", '', substr($type, strpos($type, '(')+1, strpos($type, ')') - strpos($type, '(')-1)));

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
    private function getFormTypeFromColumnType($type)
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
