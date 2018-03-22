<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Traits;

use View;
use Nickwest\EloquentForms\Theme;
use Nickwest\EloquentForms\DefaultTheme;

trait Themeable
{
    /**
     * @var Nickwest\EloquentForms\Theme
     */
    protected $Theme = null;

    /**
     * Set the theme.
     *
     * @param Nickwest\EloquentForms\Theme $Theme
     * @return void
     */
    public function setTheme(Theme $Theme): void
    {
        $this->Theme = $Theme;
    }

    /**
     * Get the theme.
     *
     * @return Nickwest\EloquentForms\Theme $Theme
     */
    public function getTheme(): Theme
    {
        return $this->Theme;
    }

    /**
     * Get the View for the given theme, or return the default.
     *
     * @param string $template
     * @param array $blade_data
     * @return Illuminate\View\View
     */
    public function getThemeView(string $template, array $blade_data = []): \Illuminate\View\View
    {
        if (View::exists($this->Theme->getViewNamespace().'::'.$template)) {
            return View::make($this->Theme->getViewNamespace().'::'.$template, $blade_data);
        } else {
            return View::make(DefaultTheme::getDefaultNamespace().'::'.$template, $blade_data);
        }
    }
}
