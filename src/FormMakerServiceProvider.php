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
        $this->loadViewsFrom(__DIR__.'/views', 'Nickwest\EloquentForms');

        Blade::directive('eloquentform_include', function($expression) {
            if(strpos($expression, ',') !== false) {
                $view = substr($expression, 0, strpos($expression, ','));
                $remainder = substr($expression, strpos($expression, ','));
            } else {
                $view = $expression;
                $remainder = '';
            }
            $template = substr($view, strpos($view, '::')+2);

            return '<?php if(View::exists('.$view.')){
                echo $__env->make('.$expression.', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            }else{
                echo $__env->make(\'Nickwest\EloquentForms::'.$template.$remainder.', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            } ?>';
        });

        Blade::directive('eloquentform_component', function($expression) {
            if(strpos($expression, ',') !== false) {
                $view = substr($expression, 0, strpos($expression, ','));
                $remainder = substr($expression, strpos($expression, ','));
            } else {
                $view = $expression;
                $remainder = '';
            }
            $template = substr($view, strpos($view, '::')+2);

            return '<?php if(View::exists('.$view.')){
                $__env->startComponent('.$expression.');
            }else{
                $__env->startComponent(\'Nickwest\EloquentForms::'.$template.$remainder.');
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

}
