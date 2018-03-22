<?php

namespace Nickwest\EloquentForms;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class EloquentFormsServiceProvider extends ServiceProvider
{
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
        $this->loadViewsFrom(__DIR__.'/views', DefaultTheme::getDefaultNamespace());

        Blade::directive('eloquentforms_include', function ($expression) {
            return '<?php if(View::exists('.EloquentFormsServiceProvider::getViewFromExpression($expression).')){
                echo $__env->make('.$expression.', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            }else{
                echo $__env->make(\''.DefaultTheme::getDefaultNamespace().'::'.substr($expression, strpos($expression, '::') + 2).', array_except(get_defined_vars(), array(\'__data\', \'__path\')))->render();
            } ?>';
        });

        Blade::directive('eloquentforms_component', function ($expression) {
            return '<?php if(View::exists('.EloquentFormsServiceProvider::getViewFromExpression($expression).')){
                $__env->startComponent('.$expression.');
            }else{
                $__env->startComponent(\''.DefaultTheme::getDefaultNamespace().'::'.substr($expression, strpos($expression, '::') + 2).');
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
     * Extract the view from a template string.
     *
     * @param string $expression
     * @return string
     */
    public static function getViewFromExpression(string $expression): string
    {
        return strpos($expression, ',') !== false ? substr($expression, 0, strpos($expression, ',')) : $expression;
    }
}
