<?php

namespace App\Livewire;

use App\Models\Instansi;
use App\Models\Penerima;
use App\Models\SP2D;
use App\Models\User;
use Livewire\Component;

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
    public $no_bg;
    public $no_rek;
    public $id_user;
    // public $netto;
  
    
    public function render()
    {
        return view('livewire.create-sp2d');
    }

    public function mount()
    {
        $this->instansi = Instansi::all();
        $this->penerima = Penerima::all();
        $this->users = User::all();

    }

    
    private function resetInputFields(){
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
        $this->no_bg = '';
        $this->no_rek = '';
        $this->id_user = '';
        // $this->dispatch('reset-tom-select');
        
    }

    public function store(){
        $rules = [
            'nomor_sp2d' => 'required|string|max:255',
            'tanggal_sp2d' => 'required|date',
            'jenis_sp2d' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'id_penerima' => 'required|exists:penerima,id',
            'id_instansi' => 'required|exists:instansi,id',
            'brutto' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph_21' => 'nullable|numeric|min:0',
            'pph_22' => 'nullable|numeric|min:0',
            'pph_23' => 'nullable|numeric|min:0',
            'pph_4' => 'nullable|numeric|min:0',
            'no_bg' => 'nullable|numeric',
            'no_rek' => 'nullable|string|max:255',
            'id_user' => 'nullable|exists:users,id',
        ];
        $validated = $this->validate($rules);
        // dd($validated);

        // Cek apakah nomor_sp2d sudah ada
        if (\App\Models\SP2D::where('nomor_sp2d', $validated['nomor_sp2d'])->exists()) {
            session()->flash('error', 'Nomor SP2D sudah ada, silakan gunakan nomor lain.');
            return;
        }

        // Cek apakah nomor_bg sudah ada
        if (\App\Models\SP2D::where('no_bg', $validated['no_bg'])->exists()) {
            session()->flash('error', 'Nomor BG sudah ada, silakan gunakan nomor lain.');
            return;
        }

        $validated['id_user'] = auth()->id();
        SP2D::create($validated);


        $this->resetInputFields();
        session()->flash('message', 'Data SP2D berhasil ditambahkan!');
        return redirect()->route('sp2d');


        // try {
        //     SP2D::create($validated);
        //     $this->resetInputFields();
        //     session()->flash('message', 'Data SP2D berhasil ditambahkan!');
            
        //     // Emit event untuk refresh komponen lain jika perlu
        //     $this->dispatch('sp2dCreated');
            
        //     return redirect()->route('sp2d');
        // } catch (\Exception $e) {
        //     session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        // }
    }
}
