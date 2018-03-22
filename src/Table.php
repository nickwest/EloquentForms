<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms;

use View;
use Route;
use Illuminate\Support\Collection;
use Nickwest\EloquentForms\Traits\Themeable;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;
use Nickwest\EloquentForms\Exceptions\InvalidRouteException;

class Table
{
    use Themeable;

    /**
     * Submit Button name (used for first submit button only).
     *
     * @var Nickwest\EloquentForms\Attributes
     */
    public $attributes = null;

    /**
     * Collection that the table will display.
     *
     * @var Illuminate\Support\Collection
     */
    public $Collection = [];

    /**
     * Array of field names.
     *
     * @var array
     */
    protected $display_fields = [];

    /**
     * Array of field names.
     *
     * @var array
     */
    protected $field_replacements = [];

    /**
     * Theme to use.
     *
     * @var string
     */
    protected $Theme = null;

    /**
     * Array of labels, keyed by field_name.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Array of css classes.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Array of column linking patterns keyed by field_name.
     *
     * @var array
     */
    protected $linking_patterns = [];

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->Theme = new DefaultTheme();
        $this->attributes = new Attributes();
    }

    /**
     * Set the Collection data to the Table Object.
     *
     * @param Illuminate\Support\Collection $Collection
     * @return void
     */
    public function setData(Collection $Collection): void
    {
        $this->Collection = $Collection;
    }

    /**
     * member mutator.
     *
     * @param array $field_names
     * @return void
     */
    public function setDisplayFields(array $field_names): void
    {
        foreach ($field_names as $field_name) {
            $this->display_fields[$field_name] = $field_name;
        }
    }

    /**
     * Display Fields Accessor.
     *
     * @return array
     */
    public function getDisplayFields(): array
    {
        return $this->display_fields;
    }

    /**
     * Add field labels to the existing labels.
     *
     * @param array $labels
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setLabels(array $labels): void
    {
        foreach ($labels as $field_name => $label) {
            if (isset($this->display_fields[$field_name])) {
                $this->labels[$field_name] = $label;
            } else {
                throw new InvalidFieldException('"'.$field_name.'" not set as a display field');
            }
        }
    }

    /**
     * Get a Label for a specific field.
     *
     * @param string $field_name
     * @return string
     */
    public function getLabel(string $field_name): string
    {
        if (isset($this->labels[$field_name])) {
            return $this->labels[$field_name];
        }

        // This should always be done the same was as Field::makeLabel()
        return ucfirst(str_replace('_', ' ', $field_name));
    }

    /**
     * Set a replacement string for a given field's output. Use {field_name} to inject values
     * field_name supports any field set in the given object/array that exists within the Collection.
     *
     * @param string $field field name
     * @param string $html non-escaped text to replace field value in output
     * @return void
     */
    public function addFieldReplacement(string $field, string $html): void
    {
        $this->field_replacements[$field] = $html;
    }

    /**
     * Check if a field has a replacement pattern.
     *
     * @param string $field field name
     * @return bool
     */
    public function hasFieldReplacement(string $field): bool
    {
        return isset($this->field_replacements[$field]);
    }

    /**
     * Get a field's replacement value.
     *
     * @param string $field field name
     * @param string $Object Object or array
     * @return string
     */
    public function getFieldReplacement(string $field, &$Object): string
    {
        $pattern = '/\{([a-zA-Z0-9_]+)\}/';
        $results = [];
        preg_match_all($pattern, $this->field_replacements[$field], $results, PREG_PATTERN_ORDER);

        $replaced = $this->field_replacements[$field];

        if (is_array($results[0]) && is_array($results[1])) {
            foreach ($results[0] as $key => $match) {
                if (is_object($Object) && isset($Object->{$results[1][$key]})) {
                    $replaced = str_replace($results[0][$key], e($Object->{$results[1][$key]}), $replaced);
                } elseif (is_array($Object) && isset($Object[$results[1][$key]])) {
                    $replaced = str_replace($results[0][$key], e($Object[$results[1][$key]]), $replaced);
                }
            }
        }

        return $replaced;
    }

    /**
     *  Convenience method for setting a linking pattern on a field.
     *
     * @param string $field_name
     * @param string $href
     */
    public function addLinkingPattern(string $field_name, string $href): void
    {
        // Make and set the linking pattern
        $this->field_replacements[$field_name] = '<a href="'.$href.'">{'.$field_name.'}</a>';
    }

    /**
     * Convenience method for creating a link replacement pattern by route name.
     *
     * @param string $field_name
     * @param string $route_name
     * @param mixed $query_string
     * @return void
     * @throws Nickwest\EloquentForms\InvalidRouteException
     */
    public function addLinkingPatternByRoute(string $field_name, string $route_name, $query_string = []): void
    {
        $Route = Route::getRoutes()->getByName($route_name);
        if ($Route === null) {
            throw new InvalidRouteException('Invalid route name '.$route_name);
        }

        // Make and set the linking pattern
        $this->field_replacements[$field_name] = '<a href="/'.$Route->uri.'">{'.$field_name.'}</a>';
    }

    /**
     * Make a view and extend $extends in $section, $blade_data is the data array to pass to View::make().
     *
     * @param array $blade_data
     * @param string $extends
     * @param string $section
     * @return Illuminate\View\View
     */
    public function makeView(array $blade_data = [], string $extends = '', string $section = ''): \Illuminate\View\View
    {
        $blade_data['Table'] = $this;
        $blade_data['extends'] = $extends;
        $blade_data['section'] = $section;

        $this->Theme->prepareTableView($this);

        $template = ($extends !== '' ? 'table-extend' : 'table');

        return $this->getThemeView($template, $blade_data);
    }
}
