<?php

namespace Nickwest\EloquentForms;

class Attributes
{
    /**
     * The key that should be used for multi fields.
     *
     * @var string
     */
    public $multi_key = null;

    /**
     * prefix for IDs when writing HTML.
     *
     * @var string
     */
    public $id_prefix = '';

    /**
     * prefix for IDs when writing HTML (used when multi_key is set).
     *
     * @var string
     */
    public $id_suffix = '';

    /**
     * Keep track of classes separately so we can build it all pretty like.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Field Attributes (defaults are set in constructor).
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Field property and attribute accessor.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function __get(string $attribute)
    {
        if ($attribute == 'class') {
            return implode(' ', $this->classes);
        } elseif ($attribute == 'id') {
            return $this->id_prefix . $this->attributes['id'] . $this->id_suffix;
        }

        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }
    }

    /**
     * Field property mutator.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $attribute, $value): void
    {
        // If it's a class, save it as an array so we can manipulate single classes
        if ($attribute == 'class') {
            if (trim($value) != '') {
                $this->classes = explode(' ', trim($value));
            } else {
                $this->classes = [];
            }

            return;
        }

        // Replace spaces in ids **TODO: Should we do this here?
        if ($attribute == 'id') {
            $value = str_replace(' ', '-', $value);
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * Field property mutator.
     *
     * @param  string  $attribute
     * @return bool
     */
    public function __isset(string $attribute): bool
    {
        if ($attribute == 'class') {
            return count($this->classes) > 0;
        }

        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * Unset an attribute.
     *
     * @param  string  $attribute
     * @return void
     */
    public function __unset(string $attribute): void
    {
        if ($attribute == 'class') {
            $this->classes = [];

            return;
        }

        unset($this->attributes[$attribute]);
    }

    /**
     * Output all attributes as a string that can be injected into a tag.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getString();
    }

    /**
     * Get the unmodified ID value.
     *
     * @return mixed
     */
    public function getRawID()
    {
        return $this->attributes['id'];
    }

    /**
     * Add a css class.
     *
     * @param  string  $class_name
     * @return void
     */
    public function addClass(string $class_name): void
    {
        if (trim($class_name) != '') {
            $this->classes[$class_name] = $class_name;
        }
    }

    /**
     * Add multiple css classes.
     *
     * @param  array  $class_names
     * @return void
     */
    public function addClasses(array $class_names): void
    {
        foreach ($class_names as $class_name) {
            $this->addClass($class_name);
        }
    }

    /**
     * Remove a css class.
     *
     * @param  string  $class_name
     * @return void
     */
    public function removeClass(string $class_name): void
    {
        unset($this->classes[$class_name]);
    }

    /**
     * Check if classes has a specific class.
     *
     * @param  string  $class_name
     * @return bool
     */
    public function hasClass(string $class_name): bool
    {
        return isset($this->classes[$class_name]);
    }

    /**
     * Output all attributes as a string.
     *
     * @param  array  $skip  Skip attributes listed here
     * @return string
     */
    public function getString(array $skip = []): string
    {
        $output = [];

        $this->prepareClassAttribute();

        if ($this->multi_key !== null) {
            $this->attributes['multiple'] = null;
        }

        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $skip)) {
                continue;
            }

            // Add [] to name attribute if there's a multi_key set or multi_key === true
            if ($key == 'name' && $this->multi_key !== null && $this->multi_key !== false) {
                $value .= '[' . ($this->multi_key !== true ? $this->multi_key : '') . ']';
            }

            if ($key == 'id') {
                $value = $this->id_prefix . $value . $this->id_suffix;
            }

            $output[] = ($value === null ? $key : $key . '="' . htmlspecialchars($value) . '"');
        }

        return implode(' ', $output);
    }

    /**
     * Convert this object to JSON representation.
     *
     * @return string JSON
     */
    public function toJson(): string
    {
        return json_encode([
            'classes' => $this->classes,
            'attributes' => $this->attributes,
            'multi_key' => $this->multi_key,
            'id_prefix' => $this->id_prefix,
            'id_suffix' => $this->id_suffix,
        ]);
    }

    /**
     * Populate this object from JSON representation.
     *
     * @param string JSON
     * @return void
     */
    public function fromJson($json): void
    {
        $array = json_decode($json);

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $this->$key = (array) $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * Prepare the class attribute using $this->classes.
     *
     * @return void
     */
    protected function prepareClassAttribute(): void
    {
        if (count($this->classes) == 0 && isset($this->attributes['class'])) {
            unset($this->attributes['class']);
        } elseif (count($this->classes) > 0) {
            $this->attributes['class'] = implode(' ', $this->classes);
        }
    }
}
