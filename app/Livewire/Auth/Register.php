<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;

class Register extends Component
{

    #[Rule('required|email:rfc,dns|unique:users')]
    public $email = '';

    #[Rule('required|min:6')]
    public $password = '';

    #[Rule('required|same:password')]
    public $passwordConfirmation = '';

    public function mount()
    {
        if (auth()->user()) {
            return redirect()->intended(route(config('proj.route_name_prefix', 'proj') . '.dashboard.index'));
        }
    }

    public function updatedEmail()
    {
        $this->validate(['email'=>'required|email:rfc,dns|unique:users']);
    }
    
    public function register()
    {
        $this->validate();

        $user = User::create([
            'email' =>$this->email,
            'password' => Hash::make($this->password),
            'remember_token' => Str::random(10),
        ]);

        auth()->login($user);

        return redirect()->intended(route(config('proj.route_name_prefix', 'proj') . '.profile.index'));
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.app');
    }
}
