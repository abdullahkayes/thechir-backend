<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class InputError extends Component
{
    public $messages;

    /**
     * Create a new component instance.
     */
    public function __construct($messages = null)
    {
        $this->messages = $messages;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('components.input-error');
    }
}
