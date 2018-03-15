<?php namespace Nickwest\EloquentForms\Traits;

use Nickwest\EloquentForms\Theme;

trait Themeable{
    /**
     * @var Nickwest\EloquentForms\Theme
     */
    protected $Theme = null;

    /**
     * Set the theme
     *
     * @param Nickwest\EloquentForms\Theme $Theme
     * @return void
     */
    public function setTheme(Theme $Theme): void
    {
        $this->Theme = $Theme;
    }

    /**
     * Get the theme
     *
     * @return Nickwest\EloquentForms\Theme $Theme
     */
    public function getTheme(): Theme
    {
        return $this->Theme;
    }

}
