<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class InputLabel extends Component
{
    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('components.input-label');
    }
}
