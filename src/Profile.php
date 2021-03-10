<?php

namespace YuriyMartini\Nova\Tools\Profile;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Profile extends Tool
{
    protected $renderNavigation = true;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('profile', __DIR__.'/../dist/js/tool.js');
    }

    /**
     * @return $this
     */
    public function withoutNavigation()
    {
        $this->renderNavigation = false;
        return $this;
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return ViewContract
     */
    public function renderNavigation()
    {
        return View::make('profile::navigation')->with('render', $this->renderNavigation);
    }
}
