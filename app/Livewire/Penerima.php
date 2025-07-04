<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Penerima as PenerimaModel;

class Penerima extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $showEditModal = false;
    #[Url(keep: true)]
    public $search = '';

    public $nama_penerima;
    public $id;


    protected $listeners = ['penerimaCreated' => '$refresh'];


    public function updateingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $data = PenerimaModel::findOrFail($id);

        $this->id = $data->id;
        $this->nama_penerima = $data->nama_penerima;

        $this->showEditModal = true;

        $this->dispatch('show-edit-modal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'nama_penerima' => 'required|string',
        ]);

        if($this->id){
            $penerima = PenerimaModel::find($this->id);
            $penerima->update($validatedData);
            session()->flash('message', 'Data Penerima berhasil diperbarui!');
            $this->closeModal();
        }
    }

    public function delete($id)
    {
        PenerimaModel::find($id)->delete();
        session()->flash('message', 'Data berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->reset();
    }

    
    public function render()
    {
        $penerima = PenerimaModel::query()
            ->where(function ($query) {
                $query->where('nama_penerima', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
        return view('livewire.penerima', ['penerima' => $penerima]);
    }
}
