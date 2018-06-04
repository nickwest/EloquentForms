<?php

namespace Nickwest\EloquentForms\Traits;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Theme;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;

trait HasFields
{
    /**
     * Array of Field Objects.
     *
     * @var array
     */
    protected $Fields = [];

    /**
     * Require getTheme method.
     */
    abstract public function getTheme(): Theme;

    /**
     * Require setTheme method.
     */
    abstract public function setTheme(Theme $Theme): void;

    /**
     * Field value accessor.
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
     * Field value isset.
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
     * Field value mutator.
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
     * Unset a field's value.
     *
     * @param string $field_nname
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return void
     */
    public function __unset(string $field_name): void
    {
        $this->setValue($field_name, null);
    }

    /**
     * get a single field.
     *
     * @param string $field_name
     * @return Nickwest\EloquentForms\Field
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getField(string $field_name): Field
    {
        if (! isset($this->Fields[$field_name])) {
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return $this->Fields[$field_name];
    }

    /**
     * get an array of all Fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->Fields;
    }

    /**
     * Add a single field to the form.
     *
     * @param string $field_name
     * @return void
     */
    public function addField(string $field_name): void
    {
        $this->Fields[$field_name] = new Field($field_name);

        // Carry over the current theme to the Field
        $this->Fields[$field_name]->setTheme($this->getTheme());
    }

    /**
     * Add a bunch of fields to the form, New fields will overwrite old ones with the same name.
     *
     * @param array $field_names
     * @return void
     */
    public function addFields(array $field_names): void
    {
        foreach ($field_names as $field_name) {
            $this->Fields[$field_name] = new Field($field_name);

            // Carry over the current theme to the Field
            $this->Fields[$field_name]->setTheme($this->getTheme());
        }
    }

    /**
     * Clone an existing field.
     *
     * @param string $field_name
     * @param string $new_name
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     * @return void
     */
    public function cloneField(string $field_name, string $new_name): void
    {
        if(!isset($this->Fields[$field_name])){
            throw new InvalidFieldException('Source field is not valid');
        }

        $this->Fields[$new_name] = clone $this->Fields[$field_name];
    }

    /**
     * Remove a single field from the form if it exists.
     *
     * @param string $field_name
     * @return void
     */
    public function removeField(string $field_name): void
    {
        if (isset($this->Fields[$field_name])) {
            unset($this->Fields[$field_name]);
        }
    }

    /**
     * Remove a bunch of fields to the form if they exist.
     *
     * @param array $field_names
     * @return void
     */
    public function removeFields(array $field_names): void
    {
        foreach ($field_names as $field_name) {
            if (isset($this->Fields[$field_name])) {
                unset($this->Fields[$field_name]);
            }
        }
    }

    /**
     * Is $field_name a field.
     *
     * @param string $field_name
     * @return bool
     */
    public function isField(string $field_name): bool
    {
        return isset($this->Fields[$field_name]) && is_object($this->Fields[$field_name]);
    }

    /**
     * Get an array of field values keyed by field name.
     *
     * @return array
     */
    public function getFieldValues(): array
    {
        $values = [];

        foreach ($this->Fields as $Field) {
            // Don't return subforms as fields they don't really have a valueaddDataList
            if (! $Field->isSubform()) {
                $values[$Field->getOriginalName()] = $Field->attributes->value;
            }
        }

        return $values;
    }

    /**
     * Set a single field's value.
     *
     * @param string $field_name
     * @param mixed $value
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setValue(string $field_name, $value): void
    {
        if (isset($this->Fields[$field_name])) {
            $this->Fields[$field_name]->attributes->value = $value;
        } else {
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }
    }

    /**
     * Get a single field's value.
     *
     * @param string $field_name
     * @return mixed
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function getValue(string $field_name)
    {
        if (! isset($this->Fields[$field_name])) {
            throw new InvalidFieldException($field_name.' is not part of the Form');
        }

        return $this->Fields[$field_name]->attributes->value;
    }

    /**
     * Set multiple field values at once [field_name] => value.
     *
     * @param array $values
     * @param bool $ignore_invalid
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setValues(array $values, bool $ignore_invalid = false): void
    {
        foreach ($values as $field_name => $value) {
            if (isset($this->Fields[$field_name])) {
                $this->Fields[$field_name]->attributes->value = $value;
            } elseif (! $ignore_invalid) {
                throw new InvalidFieldException($field_name.' is not part of the Form');
            }
        }
    }
}
