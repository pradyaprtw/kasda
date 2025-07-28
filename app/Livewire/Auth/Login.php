<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\DataCleanupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        // Coba cari user berdasarkan username
        $user = \App\Models\User::where('username', $this->username)->first();

        if (!$user) {
            session()->flash('error', 'Username tidak ditemukan.');
            return;
        }

        if (!Hash::check($this->password, $user->password)) {
            session()->flash('error', 'Password salah.');
            return;
        }

        // Kalau semua cocok, login
        Auth::login($user, true);
        return redirect()->intended();
    }

    public function authenticated(Request $request, $user)
    {
        $recentCleanup = DataCleanupLog::whereDate('deleted_at', '>=', now()->subWeek())->latest()->first();

        if ($recentCleanup) {
            session()->flash('info', '📢 Data sebelum ' . $recentCleanup->deleted_before->format('d M Y') . ' telah dihapus otomatis.');
        }

        return redirect()->intended($this->redirectPath());
    }
}
