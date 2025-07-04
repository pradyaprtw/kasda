<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\SP2D as SP2DModel;
use App\Models\Instansi;
use App\Models\Penerima;
use App\Models\User;

class Sp2d extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk kontrol modal
    public $showEditSp2d = false;

    // Properti untuk tabel
    #[Url(keep: true)] 
    public $search = '';

    public $id;
    public $nomor_sp2d, $tanggal_sp2d, $jenis_sp2d, $keterangan, $id_penerima, $id_instansi, $brutto, $ppn, $pph_21, $pph_22, $pph_23, $pph_4, $no_bg, $no_rek, $id_user;

    // Properti untuk mengisi dropdown
    public $penerima = [];
    public $instansi = [];
    public $users = [];

    protected $listeners = ['sp2dCreated' => '$refresh'];

    public function mount()
    {
        // Ambil data untuk dropdown sekali saja saat komponen dimuat
        $this->penerima = Penerima::all();
        $this->instansi = Instansi::all();
        $this->users = User::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $data = SP2DModel::findOrFail($id);
        
        $this->id = $data->id;
        $this->nomor_sp2d = $data->nomor_sp2d;
        $this->tanggal_sp2d = $data->tanggal_sp2d;
        $this->jenis_sp2d = $data->jenis_sp2d;
        $this->keterangan = $data->keterangan;
        $this->id_penerima = $data->id_penerima;
        $this->id_instansi = $data->id_instansi;
        $this->brutto = $data->brutto;
        $this->ppn = $data->ppn;
        $this->pph_21 = $data->pph_21;
        $this->pph_22 = $data->pph_22;
        $this->pph_23 = $data->pph_23;
        $this->pph_4 = $data->pph_4;
        $this->no_bg = $data->no_bg;
        $this->no_rek = $data->no_rek;
        $this->id_user = $data->id_user;

        $this->showEditSp2d = true;

        $this->dispatch('init-selects-for-edit'); 
    }

    public function update()
    {
        $validatedData = $this->validate([
            'id_user' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string',
            'nomor_sp2d' => 'required|string',
            'tanggal_sp2d' => 'required|date',
            'jenis_sp2d' => 'required|string',
            'id_instansi' => 'required|exists:instansi,id',
            'id_penerima' => 'required|exists:penerima,id',
            'no_rek' => 'required|string',
            'brutto' => 'required|numeric|min:0',
            'no_bg' => 'required|integer',
            'ppn' => 'nullable|numeric|min:0',
            'pph_21' => 'nullable|numeric|min:0',
            'pph_22' => 'nullable|numeric|min:0',
            'pph_23' => 'nullable|numeric|min:0',
            'pph_4' => 'nullable|numeric|min:0',
        ]);

        if ($this->id) {
            $sp2d = SP2DModel::find($this->id);
            $sp2d->update($validatedData);
            session()->flash('message', 'Data SP2D berhasil diperbarui.');
            $this->closeModal();
        }
    }

    public function tandaiSebagaiSesuai($id)
    {
        // Cari data berdasarkan ID
        $sp2d = SP2DModel::find($id);

        if ($sp2d) {
            // Update kolom waktu_sesuai dengan waktu saat ini
            $sp2d->waktu_sesuai = now();
            $sp2d->save();

            // Kirim pesan sukses (opsional)
            session()->flash('message', 'Nomor SP2D ' . $sp2d->nomor_sp2d . ' telah sesuai.');
        }
    }

    public function batalkanSesuai($id)
    {
        // Cari data berdasarkan ID
        $sp2d = SP2DModel::find($id);

        if ($sp2d) {
            // Kosongkan kolom waktu_sesuai
            $sp2d->waktu_sesuai = null;
            $sp2d->save();

            session()->flash('message', 'Nomor SP2D ' . $sp2d->nomor_sp2d . ' telah dibatalkan.');
        }
    }

    public function delete($id)
    {

        $sp2d=SP2DModel::find($id)->delete();
        session()->flash('message', 'Nomor SP2D ' . $sp2d->nomor_sp2d . ' berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showEditSp2d = false;
        $this->reset();
        $this->mount(); // Muat ulang data dropdown jika perlu
    }

    public function render()
    {
        $sp2d = SP2DModel::with(['penerima', 'instansi'])
            ->where(function ($query) {
                $query->where('nomor_sp2d', 'like', '%' . $this->search . '%')
                      ->orWhere('jenis_sp2d', 'like', '%' . $this->search . '%')
                      ->orWhereHas('penerima', function ($q) {
                          $q->where('nama_penerima', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('instansi', function ($q) {
                          $q->where('nama_instansi', 'like', '%' . $this->search . '%');
                      });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.sp2d', [
            'sp2d' => $sp2d,
        ]);
    }
}