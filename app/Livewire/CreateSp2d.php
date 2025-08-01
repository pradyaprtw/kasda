<?php

namespace App\Livewire;

use App\Models\Instansi;
use App\Models\Penerima;
use App\Models\SP2D;
use App\Models\User;
use Livewire\Component;
use App\Livewire\Sp2d as Sp2dList;

class CreateSp2d extends Component
{
    public $instansi;
    public $penerima;
    public $users;

    public $nomor_sp2d;
    public $tanggal_sp2d;
    public $jenis_sp2d;
    public $keterangan;
    public $id_penerima;
    public $id_instansi;
    public $brutto;
    public $ppn;
    public $pph_21;
    public $pph_22;
    public $pph_23;
    public $pph_4;
    public $iuran_wajib;
    public $iuran_wajib_2;
    public $no_bg;
    public $id_user;
    public $no_rek;

    // --- DIHAPUS: Listener ini tidak diperlukan di komponen ini ---
    // protected $listeners = ['sp2dCreated' => '$refresh'];

    public function render()
    {
        return view('livewire.create-sp2d');
    }

    public function mount()
    {
        $this->instansi = Instansi::select('id', 'nama_instansi')->get();
        $this->penerima = Penerima::select('id', 'nama_penerima', 'no_rek')->get();
        $this->users = User::select('id', 'name')->get();
    }

    public function updatedIdPenerima($penerimaId)
    {
        $selectedPenerima = Penerima::find($penerimaId);
        if ($selectedPenerima) {
            $this->no_rek = $selectedPenerima->no_rek;
        } else {
            $this->no_rek = '';
        }
    }

    private function resetInputFields()
    {
        $this->nomor_sp2d = '';
        $this->tanggal_sp2d = '';
        $this->jenis_sp2d = '';
        $this->keterangan = '';
        $this->id_penerima = '';
        $this->id_instansi = '';
        $this->brutto = '';
        $this->ppn = '';
        $this->pph_21 = '';
        $this->pph_22 = '';
        $this->pph_23 = '';
        $this->pph_4 = '';
        $this->iuran_wajib = '';
        $this->iuran_wajib_2 = '';
        $this->no_bg = '';
        $this->id_user = '';
        $this->no_rek = '';

        $this->dispatch('reset-select2');
    }

    public function store()
    {
        if (!is_numeric($this->id_penerima)) {
            $this->validate([
                'id_penerima' => 'required|string|unique:penerima,nama_penerima',
                'no_rek' => 'required|string|max:255'
            ], [
                'id_penerima.unique' => 'Nama penerima ini sudah ada.'
            ]);

            $newPenerima = Penerima::create([
                'nama_penerima' => $this->id_penerima,
                'no_rek' => $this->no_rek,
            ]);
            $this->id_penerima = $newPenerima->id;
        }

        $validatedSp2dData = $this->validate(
            [
                'tanggal_sp2d' => 'required|date',
                'jenis_sp2d' => 'required|string|max:255',
                'keterangan' => 'nullable|string|max:255',
                'id_penerima' => 'required|exists:penerima,id',
                'id_instansi' => 'required|exists:instansi,id',
                'brutto' => 'required|numeric|min:0',
                'ppn' => 'nullable|numeric',
                'pph_21' => 'nullable|numeric',
                'pph_22' => 'nullable|numeric',
                'pph_23' => 'nullable|numeric',
                'pph_4' => 'nullable|numeric',
                'iuran_wajib' => 'nullable|numeric',
                'iuran_wajib_2' => 'nullable|numeric',
                'id_user' => 'nullable|exists:users,id',
                'nomor_sp2d' => 'required|string|unique:sp2d,nomor_sp2d',
                'no_bg' => 'nullable|string|unique:sp2d,no_bg',

            ],
            [
                'nomor_sp2d.unique' => 'Nomor SP2D ini sudah ada.',
                'no_bg.unique' => 'Nomor BG ini sudah ada.',
            ]
        );

        foreach (['ppn', 'pph_21', 'pph_22', 'pph_23', 'pph_4', 'iuran_wajib', 'iuran_wajib_2'] as $field) {
            $this->$field = $this->$field === '' ? null : $this->$field;
        }

        $validatedSp2dData['id_user'] = auth()->id();

        SP2D::create($validatedSp2dData);

        session()->flash('message', 'Data SP2D berhasil ditambahkan!');
        $this->dispatch('sp2dCreated')->to(Sp2dList::class);
        $this->resetInputFields();
    }
}
