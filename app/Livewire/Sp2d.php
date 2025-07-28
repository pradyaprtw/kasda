<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\SP2D as SP2DModel;
use App\Models\Instansi;
use App\Models\Penerima;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On; // --- PERBAIKAN 1: Import atribut On ---

class Sp2d extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk kontrol modal
    public $showEditSp2d = false;

    // Properti untuk tabel
    #[Url(keep: true)]
    public $search = '';

    public $id; // This will hold the ID of the SP2D being edited
    public $nomor_sp2d, $tanggal_sp2d, $jenis_sp2d, $keterangan, $id_penerima, $id_instansi, $brutto, $ppn, $pph_21, $pph_22, $pph_23, $pph_4, $no_bg, $id_user;
    public $no_rek;

    // Properti untuk mengisi dropdown
    public $penerima = [];
    public $instansi = [];
    public $users = [];

    // --- PERBAIKAN 2: Hapus properti $listeners yang lama ---
    // protected $listeners = ['sp2dCreated' => '$refresh', 'sp2dUpdated' => '$refresh'];

    /**
     * --- PERBAIKAN 3: Gunakan atribut #[On] untuk mendengarkan event ---
     * Fungsi ini akan berjalan ketika event 'sp2dCreated' atau 'sp2dUpdated' diterima.
     * Nama fungsi bisa apa saja, tetapi isinya hanya me-refresh komponen.
     */
    #[On('sp2dCreated')]
    #[On('sp2dUpdated')]
    public function refreshComponent()
    {
        // Fungsi ini sengaja dibiarkan kosong.
        // Livewire akan secara otomatis me-render ulang komponen setelah fungsi ini dipanggil.
    }

    public function mount()
    {
        $this->penerima = Penerima::select('id', 'nama_penerima', 'no_rek')->get();
        $this->instansi = Instansi::select('id', 'nama_instansi')->get();
        $this->users = User::select('id', 'name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $data = SP2DModel::with('penerima')->findOrFail($id);

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
        $this->id_user = $data->id_user;
        $this->no_rek = $data->penerima->no_rek ?? '';

        $this->showEditSp2d = true;
        $this->dispatch('init-selects-for-edit');
    }

    public function update()
    {
        $validatedSp2dData = $this->validate([
            'keterangan' => 'nullable|string',
            'tanggal_sp2d' => 'required|date',
            'jenis_sp2d' => 'required|string',
            'id_instansi' => 'required|exists:instansi,id',
            'id_penerima' => 'required|exists:penerima,id',
            'brutto' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric',
            'pph_21' => 'nullable|numeric',
            'pph_22' => 'nullable|numeric',
            'pph_23' => 'nullable|numeric',
            'pph_4' => 'nullable|numeric',
            'nomor_sp2d' => ['required', 'string', Rule::unique('sp2d')->ignore($this->id)],
            'no_bg' => ['nullable', 'string', Rule::unique('sp2d')->ignore($this->id)],
        ]);

        $this->validate([
            'no_rek' => 'required|string',
        ]);

        if ($this->id) {
            $sp2d = SP2DModel::find($this->id);
            $penerima = Penerima::find($this->id_penerima);

            if ($sp2d && $penerima) {
                $numericFields = ['ppn', 'pph_21', 'pph_22', 'pph_23', 'pph_4'];
                foreach ($numericFields as $field) {
                    if (isset($validatedSp2dData[$field]) && $validatedSp2dData[$field] === '') {
                        $validatedSp2dData[$field] = null;
                    }
                }

                $sp2d->update($validatedSp2dData);
                $penerima->no_rek = $this->no_rek;
                $penerima->save();

                session()->flash('message', 'Data SP2D berhasil diperbarui.');
                $this->dispatch('sp2dUpdated');
                $this->closeModal();
            }
        }
    }

    public function tandaiSebagaiSesuai($id)
    {
        $sp2d = SP2DModel::find($id);
        if ($sp2d) {
            $sp2d->waktu_sesuai = now();
            $sp2d->save();
            session()->flash('message', 'Nomor SP2D ' . $sp2d->nomor_sp2d . ' telah sesuai.');
            $this->dispatch('sp2dUpdated');
        }
    }

    public function batalkanSesuai($id)
    {
        $sp2d = SP2DModel::find($id);
        if ($sp2d) {
            $sp2d->waktu_sesuai = null;
            $sp2d->save();
            session()->flash('message', 'Nomor SP2D ' . $sp2d->nomor_sp2d . ' telah dibatalkan.');
            $this->dispatch('sp2dUpdated');
        }
    }

    public function delete($id)
    {
        $sp2d = SP2DModel::find($id);
        if ($sp2d) {
            $nomor = $sp2d->nomor_sp2d;
            $sp2d->delete();
            session()->flash('message', 'Nomor SP2D ' . $nomor . ' berhasil dihapus.');
            $this->dispatch('sp2dUpdated');
        }
    }

    public function closeModal()
    {
        $this->showEditSp2d = false;
        $this->resetValidation();
        $this->resetExcept('search');
    }

    public function render()
    {
        $query = SP2DModel::query();
        $query->with([
            'penerima:id,nama_penerima,no_rek',
            'instansi:id,nama_instansi'
        ]);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nomor_sp2d', 'like', '%' . $this->search . '%')
                    ->orWhere('jenis_sp2d', 'like', '%' . $this->search . '%')
                    ->orWhere('no_bg', 'like', '%' . $this->search . '%');

                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $this->search)) {
                    $dateParts = explode('-', $this->search);
                    if (count($dateParts) === 3) {
                        $formattedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                        $q->orWhereDate('created_at', $formattedDate);
                    }
                }

                $q->orWhereHas('penerima', function ($subq) {
                    $subq->where('nama_penerima', 'like', '%' . $this->search . '%')
                        ->orWhere('no_rek', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('instansi', function ($subq) {
                        $subq->where('nama_instansi', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $query->latest('created_at');
        $sp2d = $query->select([
            'id',
            'nomor_sp2d',
            'tanggal_sp2d',
            'created_at',
            'jenis_sp2d',
            'keterangan',
            'id_penerima',
            'id_instansi',
            'brutto',
            'ppn',
            'pph_21',
            'pph_22',
            'pph_23',
            'pph_4',
            'no_bg',
            'waktu_sesuai',
            'netto'
        ])
            ->paginate(10);

        return view('livewire.sp2d', [
            'sp2d' => $sp2d,
        ]);
    }
}
