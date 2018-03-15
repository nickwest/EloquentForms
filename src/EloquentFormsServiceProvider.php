<?php namespace Nickwest\EloquentForms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class EloquentFormsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'Nickwest\\EloquentForms');

        Blade::directive('eloquentforms_include', function($expression) {
            return '<?php if(View::exists('.\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'view').')){
                echo $__env->make('.$expression.', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            }else{
                echo $__env->make(\'Nickwest\\EloquentForms::'.\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'template').\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'remainder').', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            } ?>';
        });

        Blade::directive('eloquentforms_component', function($expression) {
            return '<?php if(View::exists('.\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'view').')){
                $__env->startCompongitent('.$expression.');
            }else{
                $__env->startComponent(\'Nickwest\\EloquentForms::'.\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'template').\Nickwest\EloquentForms\EloquentFormsServiceProvider::getPartOfExpression($expression, 'remainder').');
            } ?>';
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Extract part of an expression
     *
     * @param string $expression
     * @param string $part
     * @return string
     */
    static public function getPartOfExpression(string $expression, string $part): string
    {
        if(strpos($expression, ',') !== false) {
            $view = substr($expression, 0, strpos($expression, ','));
            $remainder = substr($expression, strpos($expression, ','));
        } else {
            $view = $expression;
            $remainder = '';
        }
        $template = substr($view, strpos($view, '::')+2);

        return $$part;
    }

}
