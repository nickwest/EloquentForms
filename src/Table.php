<?php namespace Nickwest\EloquentForms;

use View;
use Route;

use Illuminate\Support\Collection;

use Nickwest\EloquentForms\Theme;
use Nickwest\EloquentForms\Traits\Themeable;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;
use Nickwest\EloquentForms\Exceptions\InvalidRouteException;

use Maatwebsite\Excel\Facades\Excel;

class Table{

    use Themeable;

    /**
     * Submit Button name (used for first submit button only)
     *
     * @var Nickwest\EloquentForms\Attributes
     */
    public $attributes = null;

    /**
     * Array of field names
     *
     * @var array
     */
    protected $display_fields = [];

    /**
     * Array of field names
     *
     * @var array
     */
    protected $field_replacements = [];

    /**
     * Theme to use
     *
     * @var string
     */
    protected $Theme = null;

    /**
     * Array of labels, keyed by field_name
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Array of css classes
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Array of column linking patterns keyed by field_name
     *
     * @var array
     */
    protected $linking_patterns = [];

    /**
     * Collection that the table will display
     *
     * @var Illuminate\Support\Collection
     */
    protected $Collection = [];

    /**
     * Excel Config values from client
     *
     * @var array
     */
    protected $excel_config = [];


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->Theme = new DefaultTheme();
        $this->attributes = new Attributes();
    }

    /**
     * Set the Collection data to the Table Object
     *
     * @param Illuminate\Support\Collection $Collection
     * @return void
     */
    public function setData(Collection $Collection): void
    {
        $this->Collection = $Collection;
    }

    /**
     * member mutator
     *
     * @param array $field_names
     * @return void
     */
    public function setDisplayFields(array $field_names): void
    {
        foreach($field_names as $field_name) {
            $this->display_fields[$field_name] = $field_name;
        }
    }

    /**
     * Display Fields Accessor
     *
     * @return array
     */
    public function getDisplayFields(): array
    {
        return $this->display_fields;
    }

    /**
     * Add field labels to the existing labels
     *
     * @param array $labels
     * @return void
     * @throws Nickwest\EloquentForms\Exceptions\InvalidFieldException
     */
    public function setLabels(array $labels): void
    {
        foreach($labels as $field_name => $label) {
            if(isset($this->display_fields[$field_name])) {
                $this->labels[$field_name] = $label;
            } else {
                throw new InvalidFieldException('"'.$field_name.'" not set as a display field');
            }
        }
    }

    /**
     * Get a Label for a specific field
     *
     * @param string $field_name
     * @return string
     */
    public function getLabel(string $field_name): string
    {
        if(isset($this->labels[$field_name])) {
            return $this->labels[$field_name];
        }

        // This should always be done the same was as Field::makeLabel()
        return ucfirst(str_replace('_', ' ', $field_name));
    }

    /**
     * Set a replacement string for a given field's output. Use {field_name} to inject values
     * field_name supports any field set in the given object/array that exists within the Collection
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
     * Check if a field has a replacement pattern
     *
     * @param string $field field name
     * @return bool
     */
    public function hasFieldReplacement(string $field): bool
    {
        return isset($this->field_replacements[$field]);
    }

    /**
     * Get a field's replacement value
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

        if(is_array($results[0]) && is_array($results[1])) {
            foreach($results[0] as $key => $match) {
                if(is_object($Object) && isset($Object->{$results[1][$key]})) {
                    $replaced = str_replace($results[0][$key], (string)$Object->{$results[1][$key]}, $replaced);
                } elseif(is_array($Object) && isset($Object[$results[1][$key]])) {
                    $replaced = str_replace($results[0][$key], $Object[$results[1][$key]], $replaced);
                }
            }
        }

        return $replaced;
    }

    /**
     * Convenience method for creating a link replacement pattern by route name
     *
     * @param string $field_name
     * @param string $route_name
     * @param mixed $query_string
     * @return void
     * @throws Nickwest\EloquentForms\InvalidRouteException
     */
    public function setLinkingPatternByRoute(string $field_name, string $route_name, $query_string=[]): void
    {
        $Route = Route::getRoutes()->getByName($route_name);
        if($Route == null) {
            throw new InvalidRouteException('Invalid route name '.$route_name);
        }

        // Make and set the linking pattern
        $this->field_replacements[$field_name] = '<a href="/'.$Route->uri.'">{'.$field_name.'}</a>';
    }


    /**
     * Make a view and extend $extends in $section, $blade_data is the data array to pass to View::make()
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

        $template = ($extends != '' ? 'table-extends' : 'table');

        if(View::exists($this->Theme->getViewNamespace().'::'.$template)) {
            return View::make($this->Theme->getViewNamespace().'::'.$template, $blade_data);
        }else{
            return View::make(DefaultTheme::getDefaultNamespace().'::'.$template, $blade_data);
        }
    }

    /**
     * Export this to excel
     *
     * @param string $title
     * @param array $config
     * @return void
     */
    public function exportToExcel(string $title, array $config): void
    {
        $this->excel_config = $config;

        foreach($this->display_fields as $field){
            $headings[] = $this->getLabel($field);
        }

        $export = array_merge([$headings], $this->Collection->map(function($item){
            $collection = new Collection($item);
            return $collection->only($this->display_fields)->all();
        })->toArray());

        Excel::create($title, function($Excel) use ($title, $config, $export) {
            $Excel->setTitle($title)
                    ->setCreator($this->config('Creator', ''))
                    ->setCompany($this->config('Company', ''))
                    ->setDescription($this->config('Description', ''));

            $Excel->sheet($title, function($Sheet) use ($config, $export){
                // Set font
                $Sheet->setFontFamily($this->config('FontFamily', 'Calibri'));
                $Sheet->setFontSize($this->config('FontSize', 16));
                $Sheet->setFontBold($this->config('FontBold', false));

                // Set up the page
                $Sheet->sethorizontalCentered($this->config('horizontalCentered', false));
                $Sheet->setfitToPage($this->config('fitToPage', false));
                $Sheet->setfitToHeight($this->config('fitToHeight', false));
                $Sheet->setfitToWidth($this->config('fitToWidth', true));
                $Sheet->setpaperSize($this->config('paperSize', 1));

                // Set margins
                $Sheet->setPageMargin($this->config('PageMargin', array(0.7, 0.25, 0.25, 0.25)));

                // Populate data
                $Sheet->fromArray($export, null, 'A1', false, false);

                $highest_col = $Sheet->getHighestColumn();

                $total_rows = count($export);

                // Format all rows as text
                $Sheet->setColumnFormat(array(
                    'A1:'.$highest_col.$total_rows => '@',
                ));

                // Add borders
                if($this->config('borders', false)){
                    $Sheet->setBorder('A1:'.$highest_col.$total_rows, 'thin');
                }

                // Make the first row bold
                $Sheet->cell('A1:'.$highest_col.'1', function($cells){
                    $cells->setFontWeight('bold');
                });

                // Zebra rows
                if($this->config('zebraRows', true)) {
                    for($i = 2; $i <= $total_rows; $i++){
                        if($i % 2 == 0){
                            $Sheet->cell('A'.$i.':'.$highest_col.$i, function($cells){
                                $cells->setBackground('#EEEEEE');
                            });
                        }
                    }
                }

                // Verticle align
                $Sheet->cell('A1:'.$highest_col.$total_rows, function($cells){
                    $cells->setValignment('top');
                });

            });

        })->export('xls');
    }

    /**
     *
     *
     * @param string $key
     * @return mixed
     */
    protected function config(string $key, $default)
    {
        return (isset($this->excel_config[$key]) ? $this->excel_config[$key] : $default);
    }
}
