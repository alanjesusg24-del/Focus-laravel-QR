<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword;
use Livewire\Attributes\Rule;

class ForgotPassword extends Component
{
    use Notifiable;

    public $mailSentAlert = false;
    public $showDemoNotification = false;

    #[Rule('required|email|exists:users', message: ['email.exists' => 'The Email Address must be in our database.'])]
    public $email = '';

    public function mount()
    {
        if (auth()->user()) {
            return redirect()->intended(route(config('proj.route_name_prefix', 'proj') . '.dashboard.index'));
        }
    }

    public function updatedEmail()
    {
        $this->validate(['email'=>'required|email|exists:users']);
    }
    public function routeNotificationForMail() {
        return $this->email;
    }
    public function recoverPassword() {
        if(env('IS_DEMO')) {
            $this->showDemoNotification = true;
        }
        else {
            $this->validate();
            $user=User::where('email', $this->email)->first();
            $this->notify(new ResetPassword($user->id));
            $this->mailSentAlert = true;
        }
    }

    public function render()
    {
        return view('livewire.forgot-password')->layout('layouts.app');
    }
}
