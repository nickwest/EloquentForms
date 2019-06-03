<?php

namespace Nickwest\EloquentForms;

use Nickwest\EloquentForms\Exceptions\OptionValueException;
use Nickwest\EloquentForms\Exceptions\InvalidOptionException;

class Options
{
    /**
     * Class(es) for the field's containing div.
     *
     * @var string
     */
    public $container_class = ''; // was 'checkbox'

    /**
     * Class(es) for the field's containing div.
     *
     * @var string
     */
    public $wrapper_class = 'option';

    /**
     * Class(es) for the field's containing div.
     *
     * @var string
     */
    public $label_class = '';

    /**
     * Array of options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Options to that are disabled inside of a radio, checkbox or other multi-option field.
     *
     * @var array
     */
    protected $disabled_options = [];

    /**
     * Get an option by key.
     *
     * @param string $key
     * @return mixed
     * @throws Nickwest\EloquentForms\Exceptions\InvalidOptionException
     */
    public function __get(?string $key)
    {
        return $this->getOption((string) $key);
    }

    /**
     * Set an option by key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(?string $key, $value): void
    {
        $this->setOption((string) $key, $value);
    }

    /**
     * Check if an option is set by key.
     *
     * @param $key
     * @return bool
     */
    public function __isset(?string $key): bool
    {
        return $this->hasOption($key);
    }

    /**
     *  Unset an option by key.
     *
     * @param $key
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidOptionException
     */
    public function __unset(?string $key): void
    {
        $this->removeOption((string) $key);
    }

    /**
     * Get an option's value.
     *
     * @param string $key
     * @return mixed
     * @throws Nickwest\EloquentForms\Exceptions\InvalidOptionException
     */
    public function getOption(?string $key)
    {
        if (! isset($this->options[(string) $key])) {
            throw new InvalidOptionException;
        }

        return $this->options[(string) $key];
    }

    /**
     * Set an option by key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setOption(?string $key, $value): void
    {
        $this->options[(string) $key] = $value;
    }

    /**
     * Check if an option is set by key.
     *
     * @param $key
     * @return bool
     */
    public function hasOption(?string $key): bool
    {
        return isset($this->options[(string) $key]);
    }

    /**
     *  Unset an option by key.
     *
     * @param string $key
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidOptionException
     */
    public function removeOption(?string $key): void
    {
        if (! isset($this->options[(string) $key])) {
            throw new InvalidOptionException;
        }

        unset($this->options[(string) $key]);
    }

    /**
     * Determine if there are options set.
     *
     * @return bool
     */
    public function hasOptions()
    {
        return count($this->options) > 0;
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options replacing all current options with those in the given array.
     *
     * @param array $options
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\OptionValueException
     */
    public function setOptions(array $options): void
    {
        $this->options = [];

        if ($options == null) {
            return;
        }

        foreach ($options as $key => $value) {
            if (is_array($value) || is_object($value) || is_resource($value)) {
                throw new OptionValueException('Option values must be strings');
            }

            $this->setOption((string) $key, $value);
        }
    }

    /**
     * Set options that should be disabled.
     *
     * @param array $keys
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidOptionException
     */
    public function setDisabledOptions(array $keys): void
    {
        $this->disabled_options = [];

        if ($keys == null) {
            return;
        }

        foreach ($keys as $key) {
            if (! isset($this->options[(string) $key])) {
                throw new InvalidOptionException;
            }

            $this->disabled_options[] = (string) $key;
        }
    }

    /**
     * Get disabled options.
     *
     * @return array
     */
    public function getDisabledOptions()
    {
        return $this->disabled_options;
    }

    /**
     * Create a json representation of options.
     *
     * @return string JSON
     */
    public function toJson(): string
    {
        return json_encode([
            'container_class' => $this->container_class,
            'wrapper_class' => $this->wrapper_class,
            'label_class' => $this->label_class,
            'options' => $this->options,
            'disabled_options' => $this->disabled_options,
        ]);
    }

    /**
     * Populate object from JSON representation.
     */
    public function fromJson(string $json): void
    {
        $array = json_decode($json);

        foreach ($array as $key => $value) {
            if ($key == 'options') {
                $this->setOptions((array) $value);
            } elseif (is_object($value)) {
                $this->$key = (array) $value;
            } else {
                $this->{(string) $key} = $value;
            }
        }
    }
}
