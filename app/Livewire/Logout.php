<?php

namespace App\Livewire;

use Livewire\Component;

class Logout extends Component
{

    public function logout() {
        auth()->logout();
        return redirect()->route(config('proj.route_name_prefix', 'proj') . '.auth.login');
    }
    public function render()
    {
        return view('livewire.logout');
    }
}
