<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Instansi as InstansiModel;

class Instansi extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $showEditModal = false;
    #[Url(keep: true)]
    public $search = '';

    public $nama_instansi;
    public $id;


    protected $listeners = ['instansiCreated' => '$refresh'];


    public function updateingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $data = InstansiModel::findOrFail($id);

        $this->id = $data->id;
        $this->nama_instansi = $data->nama_instansi;

        $this->showEditModal = true;

        $this->dispatch('show-edit-modal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'nama_instansi' => 'required|string',
        ]);

        if($this->id){
            $instansi = InstansiModel::find($this->id);
            $instansi->update($validatedData);
            session()->flash('message', 'Data Instansi berhasil diperbarui!');
            $this->closeModal();
        }
    }

    public function delete($id)
    {
        InstansiModel::find($id)->delete();
        session()->flash('message', 'Data berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->reset();
    }

    
    public function render()
    {
        $instansi = InstansiModel::query()
            ->where(function ($query) {
                $query->where('nama_instansi', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
        return view('livewire.instansi', ['instansi' => $instansi]);
    }
}
