<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $username;
    public $password;

    public function render()
    {
        return view('livewire.auth.login')->extends('layouts.app')->section('content');
    }

    public function rules()
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function login()
    {
        $this->validate();

        if (auth()->attempt(['username' => $this->username, 'password' => $this->password], true)) {
            return redirect()->intended();
        }

        return redirect()->to(RouteServiceProvider::HOME)
            ->withErrors(['username' => 'The provided credentials do not match our records.'])
            ->withInput(['username' => $this->username]);
    }
}
