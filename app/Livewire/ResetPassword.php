<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Rule;

class ResetPassword extends Component
{
    #[Rule('required|email|exists:users', message: ['email.exists' => 'The Email Address must be in our database.'])]
    public $email = '';

    #[Rule('required|min:6')]
    public $password = '';

    #[Rule('required|same:password')]
    public $passwordConfirmation = '';

    public $isPasswordChanged = false;
    public $wrongEmail = false;
    public $urlId='';

    public function updatedEmail()
    {
        $this->validate(['email'=>'required|email|exists:users']);
    }
    public function mount($id) {
        $existingUser = User::find($id);
        $this->urlId = intval($existingUser->id);
    }

    public function resetPassword() {
        $this->validate();
        $existingUser = User::where('email', $this->email)->first();
        if($existingUser && $existingUser->id == $this->urlId) {
            $existingUser->update([
                'password' => Hash::make($this->password)
            ]);
            $this->isPasswordChanged = true;
            $this->wrongEmail = false;
        }
        else {
            $this->wrongEmail = true;
        }
    }
    
    public function render()
    {
        return view('livewire.reset-password')->layout('layouts.app');
    }
}
