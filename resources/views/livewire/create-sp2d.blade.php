<div class="card">
    <div class="card-header">
        Tambah Data SP2D
    </div>
    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form wire:submit.prevent="store">
            <div class="mb-3">
                <label class="form-label">Staf</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal_sp2d" class="form-label">Tanggal SP2D</label>
                        <input type="date" class="form-control @error('tanggal_sp2d') is-invalid @enderror"
                            id="tanggal_sp2d" wire:model="tanggal_sp2d" required>
                        @error('tanggal_sp2d')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_sp2d" class="form-label">Nomor SP2D</label>
                        <input type="text" class="form-control @error('nomor_sp2d') is-invalid @enderror"
                            id="nomor_sp2d" wire:model="nomor_sp2d" required>
                        @error('nomor_sp2d')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
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
                        @error('jenis_sp2d')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" wire:model="keterangan">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3" wire:ignore>
                        <label for="select-penerima" class="form-label">Nama CV/Penerima</label>
                        <select id="select-penerima" class="form-control" required>
                            <option value="">Cari atau buat penerima baru...</option>
                            @foreach ($penerima as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_penerima }}</option>
                            @endforeach
                        </select>
                        @error('id_penerima')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3" wire:ignore>
                        <label for="select-instansi" class="form-label">Nama Instansi</label>
                        <select id="select-instansi" class="form-control" required>
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
                        <input type="text" class="form-control @error('no_bg') is-invalid @enderror" id="no_bg"
                            wire:model="no_bg" required>
                        @error('no_bg')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!-- =================================================================== -->
                <!-- PERBAIKAN UTAMA: Mengganti dropdown no_rek dengan input teks cerdas -->
                <!-- =================================================================== -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="no_rek" class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control @error('no_rek') is-invalid @enderror" id="no_rek"
                            wire:model="no_rek" required
                            {{-- Jika ID penerima adalah angka (artinya sudah ada), buat field ini readonly --}} @if (is_numeric($id_penerima) && $id_penerima) readonly @endif>
                            <small style="color: red">Nomor rekening akan terisi otomatis atau isi lagi untuk penerima baru</small>
                        @error('no_rek')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
                    @error('brutto')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
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
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store">Tambah</span>
                    <span wire:loading wire:target="store">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        // Menggunakan 'livewire:navigated' agar Select2 diinisialisasi ulang
        // saat navigasi menggunakan Livewire v3
        document.addEventListener('livewire:navigated', function() {

            // Hancurkan instance Select2 yang mungkin sudah ada untuk mencegah duplikasi
            if ($('#select-penerima').data('select2')) {
                $('#select-penerima').select2('destroy');
            }
            if ($('#select-instansi').data('select2')) {
                $('#select-instansi').select2('destroy');
            }

            // Inisialisasi Select2 untuk Penerima
            $('#select-penerima').select2({
                placeholder: 'Cari atau buat penerima baru...',
                tags: true, // Memungkinkan pengguna membuat tag/penerima baru
                width: '100%'
            }).on('change', function() {
                // Kirim nilai yang dipilih ke properti Livewire
                @this.set('id_penerima', $(this).val());
            });

            // Inisialisasi Select2 untuk Instansi
            $('#select-instansi').select2({
                placeholder: 'Cari atau pilih instansi...',
                width: '100%'
            }).on('change', function() {
                @this.set('id_instansi', $(this).val());
            });

            // --- DIHAPUS: Inisialisasi untuk select-no-rek tidak lagi diperlukan ---

            // Reset Select2 ketika form direset oleh Livewire
            Livewire.on('reset-select2', () => {
                $('#select-penerima').val('').trigger('change');
                $('#select-instansi').val('').trigger('change');
                // --- DIHAPUS: Reset untuk select-no-rek tidak lagi diperlukan ---
            });
        });
    </script>
@endpush
