<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Request;

class Breadcrumb extends Component
{
    public $links;
    public $currentUrl;
    /**
     * Create a new component instance.
     */
    public function __construct($links = [])
    {
        $this->links = $links;
        $this->currentUrl = Request::url();
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
