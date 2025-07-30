<?php

namespace App\Livewire;

use Livewire\Component;

class CreateInstansi extends Component
{
    public $nama_instansi;
    
    public function render()
    {
        return view('livewire.create-instansi');
    }

    public function resetInputFields()
    {
        $this->nama_instansi = '';
    }

    public function store()
    {
        $rules = [
            'nama_instansi' => 'required|string|max:255',
        ];
        $validated = $this->validate($rules);

        \App\Models\Instansi::create($validated);

        $this->resetInputFields();
        session()->flash('message', 'Data instansi berhasil ditambahkan!');
        return redirect()->route('instansi');
    }

}
