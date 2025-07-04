<!-- resources/views/livewire/create-modal.blade.php -->
<div class="modal fade" id="createSp2d" tabindex="-1" aria-labelledby="createSp2dLabel" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createSp2dLabel">Tambah Sp2d</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Display success message --}}
                @if (session()->has('message'))
                    <div class="alert alert-success mt-3 mx-3">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form wire:submit.prevent="store">
                    <input type="hidden" wire:model="id_user" value="{{ auth()->user()->id }}">
                    <div class="mb-3">
                        <label class="form-label">Petugas</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_sp2d" class="form-label">Tanggal SP2D</label>
                                <input type="date" class="form-control" id="tanggal_sp2d" wire:model="tanggal_sp2d"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_sp2d" class="form-label">Nomor SP2D</label>
                                <input type="text" class="form-control" id="nomor_sp2d" wire:model="nomor_sp2d"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" wire:model="keterangan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_sp2d" class="form-label">Jenis SP2D</label>
                                <select class="form-select" id="jenis_sp2d" wire:model="jenis_sp2d" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="GU">GU</option>
                                    <option value="UP">UP</option>
                                    <option value="LS">LS</option>
                                    <option value="TU">TU</option>
                                    <option value="gaji">Gaji</option>
                                    <option value="PFK">PFK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3" wire:ignore>
                                <label for="select-penerima" class="form-label">Nama CV/Penerima</label>
                                <select id="select-penerima" placeholder="Cari atau pilih penerima...">
                                    <option value="">Cari atau pilih penerima...</option>
                                    @foreach ($penerima as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_penerima }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_penerima')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col md-6">
                            <div class="mb-3" wire:ignore>
                                <label for="select-instansi" class="form-label">Instansi</label>
                                <select class="select-instansi" placeholder="Cari atau pilih instansi">
                                    <option value="">Cari atau Pilih Instansi</option>
                                    @foreach ($instansi as $i)
                                        <option value="{{ $i->id }}">{{ $i->nama_instansi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_instansi')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_bg" class="form-label">No BG</label>
                                <input type="number" class="form-control" id="no_bg" wire:model="no_bg" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_rek" class="form-label">No Rekening</label>
                                <input type="text" class="form-control" id="no_rek" wire:model="no_rek"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brutto" class="form-label">Bruto</label>
                                <input type="number" step="0.01" class="form-control" id="brutto"
                                    wire:model="brutto" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ppn" class="form-label">PPN</label>
                                <input type="number" step="0.01" class="form-control" id="ppn"
                                    wire:model="ppn">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="pph_21" class="form-label">PPH 21</label>
                                <input type="number" step="0.01" class="form-control" id="pph_21"
                                    wire:model="pph_21">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="pph_22" class="form-label">PPH 22</label>
                                <input type="number" step="0.01" class="form-control" id="pph_22"
                                    wire:model="pph_22">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="pph_23" class="form-label">PPH 23</label>
                                <input type="number" step="0.01" class="form-control" id="pph_23"
                                    wire:model="pph_23">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="pph_4" class="form-label">PPH 4</label>
                                <input type="number" step="0.01" class="form-control" id="pph_4"
                                    wire:model="pph_4">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="store">Simpan</span>
                            <span wire:loading wire:target="store">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div> --}}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', function() {
                // Inisialisasi TomSelect pada elemen dengan ID 'select-penerima'
                var tomselectPenerima = new TomSelect('#select-penerima', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                tomselectPenerima.on('change', function(value) {
                    @this.set('id_penerima', value);
                });

                // Inisialisasi TomSelect pada elemen dengan class 'select-instansi'
                var tomselectInstansi = new TomSelect('.select-instansi', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                tomselectInstansi.on('change', function(value) {
                    @this.set('id_instansi', value);
                });

                // Listener untuk mereset TomSelect jika form di-reset dari backend
                Livewire.on('reset-tom-select', () => {
                    tomselectPenerima.clear();
                    tomselectInstansi.clear();
                });
            });
        </script>
    @endpush
</div>
