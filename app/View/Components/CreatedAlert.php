<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CreatedAlert extends Component
{
    public $msg;

    public $type;

    public function __construct($msg = '', $type = 'success')
    {
        $this->$msg = $msg;
        $this->$type = $type;
    }

    public function render()
    {
        return view('components.created-alert');
    }
}
