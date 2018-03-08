<?php namespace Nickwest\EloquentForms;

class Attributes{

    /**
     * The key that should be used for multi fields
     *
     * @var string
     */
    public $multi_key = null;

    /**
     * prefix for IDs when writing HTML
     *
     * @var string
     */
    public $id_prefix = 'input-';

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
     * @param string $attribute
     * @param mixed $value
     * @return void
     */
    public function __set(string $attribute, $value)
    {
        // If it's a class, save it as an array so we can manipulate single classes
        if($attribute == 'class') {
            $this->classes = explode(' ', $value);
            return;
        }

        // Replace spaces in ids **TODO: Should we do this here?
        if($attribute == 'id') {
            $value = str_replace(' ', '-', $value);
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * Field property mutator
     *
     * @param string $attribute
     * @return boolean
     */
    public function __isset(string $attribute)
    {
        if($attribute == 'class'){
            return count($this->classes) > 0;
        }

        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * Unset an attribute
     *
     * @param string $attribute
     * @return void
     */
    public function __unset(string $attribute)
    {
        if($attribute == 'class'){
            $this->classes = [];
            return;
        }

        unset($this->attributes[$attribute]);
    }

    /**
     * Output all attributes as a string that can be injected into a tag
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->getString();
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
        unset($this->classes[$class_name]);
    }

    /**
     * Check if classes has a specific class
     *
     * @param string $class_name
     * @return boolean
     */
    public function hasClass(string $class_name)
    {
        return isset($this->classes[$class_name]);
    }

    /**
     * Output all attributes as a string
     *
     * @return string
     */
    public function getString(){
        $output = [];

        if(count($this->classes) > 0 && isset($this->attributes['class'])) {
            unset($this->attributes['class']);
        }elseif(count($this->classes) > 0){
            $this->attributes['class'] = implode(' ', $this->classes);
        }

        foreach($this->attributes as $key => $value) {

            // Add [] to name attribute if there's a multi_key set or multi_key === true
            if($key == 'name'){
                if($this->multi_key !== null && $this->multi_key !== false){
                    $value .= '['.($this->multi_key !== true ? $this->multi_key : '').']';
                }
            }

            if($key == 'id' && $this->id_prefix != ''){
                $value = $this->id_prefix.$value;
            }

            $output[] = ($value === null ? $key : $key.'="'.$value.'"');
        }

        return implode(' ', $output);
    }

    /**
     * Convert this object to JSON representation
     *
     * @return string JSON
     */
    public function toJson()
    {
        return json_encode([
            'classes' => $this->classes,
            'attributes' => $this->attributes,
            'multi_key' => $this->multi_key,
        ]);
    }

    /**
     * Populate this object from JSON representation
     *
     * @param string JSON
     * @return void
     */
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

}
