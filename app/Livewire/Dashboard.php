<?php

namespace App\Livewire;
use App\Models\SP2D;
use App\Models\User;

use Livewire\Component;

class Dashboard extends Component
{
    public $name;
    public function render()
    {
        // return view('livewire.dashboard', [
        //     'jumlah_berkas_masuk' => SP2D::count(),
        //     'jumlah_meja1' => User::where($this->name, 'tata'),
        //     'jumlah_meja2' => User::where($this->name, 'edy'),
        // ]);
    }
}
