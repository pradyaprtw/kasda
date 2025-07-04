<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Validation\Rule;

class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;


    public function render()
    {
        return view('livewire.auth.register')->extends('layouts.app')->section('content');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);
        auth()->login($user, true);
        return redirect()->to(RouteServiceProvider::HOME);
    }
}
