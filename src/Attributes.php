<?php namespace Nickwest\EloquentForms;

class Attributes{
    /**
     * Valid field attributes (HTML5)
     *
     * @var string
     */
    protected $valid_attributes = [
        'global' => [
            'accesskey', 'class', 'contenteditable', 'contextmenu', 'data-*', 'dir',
            'draggable', 'dropzone', 'hidden', 'id', 'lang', 'spellcheck', 'style',
            'tabindex', 'title', 'translate',
        ],
        'textarea' => [
            'autofocus', 'cols', 'dirname', 'disabled', 'form', 'maxlength', 'name',
            'readonly', 'required', 'rows', 'wrap',
        ],
        'select' => [
            'autofocus', 'disabled', 'form', 'multiple', 'name', 'required',
        ],
        'input' => [
            'autofocus', 'disabled', 'list', 'maxlength', 'name', 'readonly',
            'type', 'value',
        ],
        'button' => [

        ],
        'checkbox' => [
            'checked', 'required',
        ],
        'color' => [
            'autocomplete', 'required',
        ],
        'date' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
        'datetime' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
        'datetime-local' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
        'email' => [
            'autocomplete', 'multiple', 'pattern', 'placeholder', 'required', 'size',
        ],
        'file' => [
            'accept', 'multiple', 'required',
        ],
        'hidden' => [

        ],
        'image' => [
            'align', 'alt', 'height', 'src', 'width',
        ],
        'month' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
        'number' => [
             'max', 'min', 'required', 'step',
        ],
        'password' => [
            'autocomplete', 'pattern', 'placeholder', 'required', 'size',
        ],
        'radio' => [
            'checked', 'required',
        ],
        'range' => [
            'autocomplete', 'max', 'min', 'step',
        ],
        'reset' => [

        ],
        'search' => [
            'autocomplete', 'pattern', 'placeholder', 'required', 'size',
        ],
        'submit' => [

        ],
        'tel' => [
            'autocomplete', 'pattern', 'placeholder', 'required', 'size',
        ],
        'text' => [
            'autocomplete', 'dirname', 'pattern', 'placeholder', 'required', 'size',
        ],
        'time' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
        'url' => [
            'autocomplete', 'pattern', 'placeholder', 'required', 'size',
        ],
        'week' => [
            'autocomplete', 'max', 'min', 'pattern', 'required', 'step',
        ],
    ];

    protected $flat_attributes = [
        'checked', 'disabled', 'multiple', 'readonly', 'required', 'selected'
    ];

    public $multi_key = null;

    /**
     * Keep track of classes separately so we can build it all pretty like
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Field Attributes (defaults are set in constructor)
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Field property and attribute accessor
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get(string $attribute)
    {
        if($attribute == 'class') {
            return implode(' ', $this->classes);
        }

        if(isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }

        return null;
    }

    /**
     * Field property mutator
     *
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws \Exception
     */
    public function __set(string $attribute, $value)
    {
        // We don't validate attributes when setting them, we only do that when generating a string for the given field type
        if($attribute == 'class') {
            $this->classes = explode(' ', $value);
            return;
        }

        // replace spaces in ids
        if($attribute == 'id') {
            $value = str_replace(' ', '-', $value);
        }

        $this->attributes[$attribute] = $value;
        return;
// TODO: WTF?
        throw new \Exception('"'.$attribute.'" is not a valid attribute');
    }

    /**
     * Output all attributes as a string
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->getString();
    }

    public function toJson()
    {
        return json_encode([
            'classes' => $this->classes,
            'attributes' => $this->attributes,
        ]);
    }

    public function fromJson($json)
    {
        $array = json_decode($json);
        foreach($array as $key => $value) {
            if(is_object($value)) {
                $this->$key = (array)$value;
            } else {
                $this->$key = $value;
            }
        }
    }


    /**
     * Add a css class
     *
     * @param string $class_name
     * @return void
     */
    public function addClass(string $class_name)
    {
        if(trim($class_name) != '') {
            $this->classes[$class_name] = $class_name;
        }
    }

    /**
     * Remove a css class
     *
     * @param string $class_name
     * @return void
     */
    public function removeClass(string $class_name)
    {
        if(isset($this->classes['class_name'])) {
            unset($this->classes[$class_name]);
        }
    }

    /**
     * Output all attributes as a string
     *
     * @return string
     */
    public function getString(){
        $output = [];

        if(count($this->classes) > 0) {
            $this->attributes['class'] = '';
        }


        foreach($this->attributes as $key => $value) {
            // Skip invalid attributes (they're not HTML valid, so don't ouput them)
            if(!$this->isValidAttribute($key)) {
                continue;
            }

            if($key == 'class') {
                $value = implode(' ', $this->classes);
            }

            if($key == 'name'){
                if((isset($this->attributes['multiple']) && $this->attributes['multiple'])) {
                    $value .= '[]';
                }
                if($this->multi_key !== null && $this->multi_key !== false){
                    $value .= '['.($this->multi_key !== true ? $this->multi_key : '').']';
                }
            }

            if(in_array($key, $this->flat_attributes)) {
                if($value) {
                    $output[] = $key;
                }
            } else {
                if($key == 'value' && $this->attributes['type'] == 'datetime-local' && $value != null) {
                    $value = date('Y-m-d\TH:i', strtotime($value));
                }
                $output[] = $key.'="'.$value.'"';
            }
        }

        return implode(' ', $output);
    }

    /**
     * Check if the property exists
     *
     * @param string $key
     * @return bool
     */
    public function attributeExists($key)
    {
        foreach($this->valid_attributes as $valid_attributes) {
            foreach($valid_attributes as $attribute) {
                if($attribute == $key) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if the attribute is valid for the given field type
     *
     * @param string $key
     * @return bool
     */
    public function isValidAttribute($key)
    {
        if(isset($this->attributes['type']) && $this->attributes['type'] == 'textarea') {
            $valid_attributes = array_merge($this->valid_attributes['global'], $this->valid_attributes['textarea']);
        } elseif(isset($this->attributes['type']) && $this->attributes['type'] == 'select') {
            $valid_attributes = array_merge($this->valid_attributes['global'], $this->valid_attributes['select']);
        } else {
            $valid_attributes = array_merge($this->valid_attributes['global'], $this->valid_attributes['input']);
            if(isset($this->attributes['type']) && isset($this->valid_attributes[$this->attributes['type']])) {
                $valid_attributes = array_merge($valid_attributes, $this->valid_attributes[$this->attributes['type']]);
            }
        }

        foreach($valid_attributes as $attribute) {
            if($attribute == $key) {
                return true;
            }
        }
        return false;
    }
}
