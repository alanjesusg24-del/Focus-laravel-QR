<?php

namespace App\Livewire;

use Livewire\Component;

class Err500 extends Component
{
    public function render()
    {
        return view('500')->layout('layouts.app');
    }
}
