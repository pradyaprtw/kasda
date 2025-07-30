<?php

namespace App\Livewire;

use App\Models\Penerima as PenerimaModel;
use Livewire\Component;

class CreatePenerima extends Component
{

    public $nama_penerima;
    public $no_rek;

    public function render()
    {
        return view('livewire.create-penerima');
    }


    public function resetInputFields(){
        $this->nama_penerima = '';
        $this->no_rek = '';
    }

    public function store(){
        $rules = [
            'nama_penerima' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
        ];
        $validated = $this->validate($rules);


        PenerimaModel::create($validated);

        $this->resetInputFields();
        session()->flash('message', 'Data penerima berhasil ditambahkan!');
        return redirect()->route('penerima');
    }

}
