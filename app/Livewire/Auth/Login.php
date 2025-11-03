<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Rule;

class Login extends Component
{

    #[Rule('required|email:rfc,dns')]
    public $email = '';

    #[Rule('required|min:6')]
    public $password = '';

    public $remember_me = false;

    //This mounts the default credentials for the admin. Remove this section if you want to make it public.
    public function mount()
    {
        if (auth()->user()) {
            // Redirigir al dashboard con el nuevo esquema de rutas
            return redirect()->intended(route(config('proj.route_name_prefix', 'proj') . '.dashboard.index'));
        }
        $this->fill([
            'email' => 'admin@volt.com',
            'password' => 'secret',
        ]);
    }

    public function login()
    {
        $credentials = $this->validate();
        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember_me)) {
            $user = User::where(['email' => $this->email])->first();
            auth()->login($user, $this->remember_me);
            // Redirigir al dashboard con el nuevo esquema de rutas
            return redirect()->intended(route(config('proj.route_name_prefix', 'proj') . '.dashboard.index'));
        } else {
            return $this->addError('email', trans('auth.failed'));
        }
    }

    public function render()
    {
        // Aplicar layout de la app; el contenido se inyectará vía $slot
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
