<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class loginCard extends Component
{
    public $title;
    public $action;
    public $buttonClass;
    public $registerRoute;
    public $forgotRoute;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $action, $buttonClass = 'primary', $registerRoute = null, $forgotRoute = null)
    {
        $this->title = $title;
        $this->action = $action;
        $this->buttonClass = $buttonClass;
        $this->registerRoute = $registerRoute;
        $this->forgotRoute = $forgotRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.login-card');
    }
}
