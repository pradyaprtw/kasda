<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;


class ChangePassword extends Component
{
    public $show = false;
    public $step = 1;
    public $success = false;

    public $username = '';
    public $foundUser;
    public $new_password = '';
    public $new_password_confirmation = '';
    public $is_loading = false;


    #[On('openForgotPasswordModal')]
    public function openModal()
    {
        $this->resetForm();
        $this->show = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->show = false;
    }

    public function checkUsername()
    {
        $this->is_loading = true;

        $this->validate([
            'username' => 'required|exists:users,username'
        ], [
            'username.exists' => 'Username tidak ditemukan.',
            'username.required' => 'Username harus diisi.'
        ]);

        $this->foundUser = User::where('username', $this->username)->first();
        $this->step = 2;

        $this->is_loading = false;
    }

    public function resetPassword()
    {
        if (!$this->foundUser) {
            return;
        }

        $this->validate([
            'new_password' => 'required|min:8|confirmed'
        ]);

        $this->foundUser->password = Hash::make($this->new_password);
        $this->foundUser->save();

        $this->success = true; // Tampilkan pesan sukses
    }

    public function resetForm()
    {
        $this->reset(
            'step',
            'success',
            'username',
            'foundUser',
            'new_password',
            'new_password_confirmation',
            'is_loading'
        );
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.auth.change-password')
            ->extends('layouts.app')
            ->section('content');
    }
}
