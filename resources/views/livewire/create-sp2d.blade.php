<!-- resources/views/livewire/create-modal.blade.php -->
<div class="card">
    <div class="card-header">
        Tambah Data SP2D
    </div>
    <div class="card-body">
        <form wire:submit.prevent="store">
            <input type="hidden" wire:model="id_user" value="{{ auth()->user()->id }}">
            <div class="mb-3">
                <label class="form-label">Staf</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal_sp2d" class="form-label">Tanggal SP2D</label>
                        <input type="date" class="form-control" id="tanggal_sp2d" wire:model="tanggal_sp2d" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_sp2d" class="form-label">Nomor SP2D</label>
                        <input type="text" class="form-control" id="nomor_sp2d" wire:model="nomor_sp2d" required>
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
                        <select id="select-penerima" class="form-control js-example-tags">
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
                <div class="col-md-6">
                    <div class="mb-3" wire:ignore>
                        <label for="select-instansi" class="form-label">Instansi</label>
                        <select id="select-instansi" class="form-control js-example-tags">
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
                        <input type="text" class="form-control" id="no_rek" wire:model="no_rek" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="brutto" class="form-label">Bruto</label>
                        <input type="number" step="0.01" class="form-control" id="brutto" wire:model="brutto"
                            required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="ppn" class="form-label">PPN</label>
                        <input type="number" step="0.01" class="form-control" id="ppn" wire:model="ppn">
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
@push('scripts')
<script>
    document.addEventListener('livewire:navigated', function() {
        // Inisialisasi Select2
        $('#select-penerima').select2({
            placeholder: 'Cari atau pilih penerima...',
            tags: true,
            width: '100%'
        }).on('change', function() {
            @this.set('id_penerima', $(this).val());
        });

        $('#select-instansi').select2({
            placeholder: 'Cari atau pilih instansi...',
            width: '100%'
        }).on('change', function() {
            @this.set('id_instansi', $(this).val());
        });

        // Reset Select2 ketika form direset
        Livewire.on('reset-select2', () => {
            $('#select-penerima').val('').trigger('change');
            $('#select-instansi').val('').trigger('change');
        });
    });
</script>
@endpush
</div>

