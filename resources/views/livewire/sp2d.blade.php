<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- ==================== FORM PENCARIAN ==================== --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Cari No. SP2D, Penerima, Instansi, Tanggal...">
            </div>
        </div>
    </div>
    {{-- ========================================================= --}}

    <div class="my-3 p-3 bg-body rounded shadow-sm border border-black">
        <div class="table-responsive">
            <table class="table w-100 table-bordered table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr class="border border-white">
                        <th scope="col">No</th>
                        <th scope="col">Nomor/Tanggal SP2D</th>
                        <th scope="col">Tanggal Berkas Masuk</th>
                        <th scope="col">Jenis Sp2d</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Nama CV/Penerima</th>
                        <th scope="col">Nama Instansi</th>
                        <th scope="col">No Rek</th>
                        <th scope="col">Bruto</th>
                        <th scope="col">PPN</th>
                        <th scope="col">PPH 21</th>
                        <th scope="col">PPH 22</th>
                        <th scope="col">PPH 23</th>
                        <th scope="col">PPH 4</th>
                        <th scope="col">No BG</th>
                        <th scope="col">Netto</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ==================== TAMPILKAN DATA SP2D ==================== --}}
                    @forelse ($sp2d as $index => $ds)
                        <tr class="text-center border border-black">
                            {{-- Menampilkan nomor urut baris berdasarkan halaman --}}
                            <th scope="row" class="text-center">{{ $sp2d->firstItem() + $index }}</th>
                            {{-- Menampilkan nomor SP2D dan tanggal SP2D dalam format 'dd-mm-yyyy' --}}
                            <td>{{ $ds->nomor_sp2d . '/' . \Carbon\Carbon::parse($ds->tanggal_sp2d)->format('d-m-Y') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($ds->created_at)->format('d-m-Y') }}</td>
                            <td>{{ $ds->jenis_sp2d }}</td>
                            <td>{{ $ds->keterangan }}</td>
                            <td>{{ $ds->penerima->nama_penerima ?? '-' }}</td>
                            <td>{{ $ds->instansi->nama_instansi ?? '-' }}</td>
                            <!-- =================================================================== -->
                            <!-- PERBAIKAN UTAMA: Ambil no_rek dari relasi 'penerima' -->
                            <!-- =================================================================== -->
                            <td>{{ $ds->penerima->no_rek ?? '-' }}</td>
                            <td>Rp{{ number_format($ds->brutto, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($ds->ppn, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($ds->pph_21, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($ds->pph_22, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($ds->pph_23, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($ds->pph_4, 0, ',', '.') }}</td>
                            <td>{{ $ds->no_bg }}</td>
                            <td>Rp{{ number_format($ds->netto, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if (is_null($ds->waktu_sesuai))
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" style="border-color: #000;" type="checkbox"
                                            wire:click="tandaiSebagaiSesuai({{ $ds->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="tandaiSebagaiSesuai({{ $ds->id }})"
                                            title="Tandai sebagai sesuai">
                                        <div wire:loading wire:target="tandaiSebagaiSesuai({{ $ds->id }})">
                                            <span class="spinner-border spinner-border-sm ms-2" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-success">
                                        <i class="bi bi-check me-1"></i>Sudah Sesuai
                                    </span>
                                    <span class="text-danger">{{ auth()->user()->name }}</span>
                                    <small
                                        class="d-block text-muted">{{ \Carbon\Carbon::parse($ds->waktu_sesuai)->timezone('Asia/Jakarta')->format('d/m/y H:i') }}</small>
                                    <br>
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        wire:click="batalkanSesuai({{ $ds->id }})" wire:loading.attr="disabled"
                                        wire:target="batalkanSesuai({{ $ds->id }})" title="Kembalikan ke awal">
                                        <i class="bi bi-x-circle me-1"></i>Batal
                                    </button>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm me-1"
                                    wire:click="edit({{ $ds->id }})" wire:loading.attr="disabled"
                                    wire:target="edit({{ $ds->id }})">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm mt-2"
                                    wire:click="delete({{ $ds->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus data ini?"
                                    wire:loading.attr="disabled" wire:target="delete({{ $ds->id }})">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <p class="mb-0">Data tidak ditemukan.</p>
                                    @if ($search)
                                        <small>Pencarian: "{{ $search }}"</small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $sp2d->links() }}
    </div>

    {{-- ============= MODAL EDIT =================== --}}
    @if ($showEditSp2d)
        <div class="modal fade show" id="editSp2d" tabindex="-1" aria-labelledby="editSp2dLabel" aria-hidden="true"
            style="display: block" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editSp2dLabel">Edit SP2D</h1>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="update">
                            <div class="mb-3">
                                <label class="form-label">Petugas</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}"
                                    readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_sp2d" class="form-label">Tanggal SP2D</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_sp2d') is-invalid @enderror"
                                            id="tanggal_sp2d" wire:model="tanggal_sp2d">
                                        @error('tanggal_sp2d')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_sp2d" class="form-label">Nomor SP2D</label>
                                        <input type="text"
                                            class="form-control @error('nomor_sp2d') is-invalid @enderror"
                                            id="nomor_sp2d" wire:model="nomor_sp2d">
                                        @error('nomor_sp2d')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_sp2d" class="form-label">Jenis SP2D</label>
                                        <select class="form-select @error('jenis_sp2d') is-invalid @enderror"
                                            id="jenis_sp2d" wire:model="jenis_sp2d">
                                            <option value="">Pilih Jenis</option>
                                            <option value="GU">GU</option>
                                            <option value="UP">UP</option>
                                            <option value="LS">LS</option>
                                            <option value="TU">TU</option>
                                            <option value="gaji">Gaji</option>
                                            <option value="PFK">PFK</option>
                                        </select>
                                        @error('jenis_sp2d')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <input type="text"
                                            class="form-control @error('keterangan') is-invalid @enderror"
                                            id="keterangan" wire:model="keterangan">
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3" wire:ignore>
                                        <label for="edit-select-penerima" class="form-label">Nama CV/Penerima</label>
                                        <select id="edit-select-penerima" placeholder="Cari atau pilih penerima...">
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
                                        <label for="edit-select-instansi" class="form-label">Nama Instansi</label>
                                        <select id="edit-select-instansi" placeholder="Cari atau pilih instansi">
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
                                        <input type="text"
                                            class="form-control @error('no_bg') is-invalid @enderror" id="no_bg"
                                            wire:model="no_bg">
                                        @error('no_bg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_rek" class="form-label">No Rekening</label>
                                        <input type="text"
                                            class="form-control @error('no_rek') is-invalid @enderror" id="no_rek"
                                            wire:model="no_rek">
                                        @error('no_rek')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Sisanya form keuangan (brutto, ppn, dll.) tetap sama -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="brutto" class="form-label">Brutto</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('brutto') is-invalid @enderror" id="brutto"
                                            wire:model="brutto">
                                        @error('brutto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ppn" class="form-label">PPN</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('ppn') is-invalid @enderror" id="ppn"
                                            wire:model="ppn">
                                        @error('ppn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="pph_21" class="form-label">PPH 21</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('pph_21') is-invalid @enderror" id="pph_21"
                                            wire:model="pph_21">
                                        @error('pph_21')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="pph_22" class="form-label">PPH 22</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('pph_22') is-invalid @enderror" id="pph_22"
                                            wire:model="pph_22">
                                        @error('pph_22')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="pph_23" class="form-label">PPH 23</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('pph_23') is-invalid @enderror" id="pph_23"
                                            wire:model="pph_23">
                                        @error('pph_23')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="pph_4" class="form-label">PPH 4</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('pph_4') is-invalid @enderror" id="pph_4"
                                            wire:model="pph_4">
                                        @error('pph_4')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2"
                                    wire:click="closeModal">Batal</button>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="update">Simpan</span>
                                    <span wire:loading wire:target="update">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" wire:click="closeModal"></div>
    @endif

    @push('scripts')
        <script>
            function initTomSelectEdit() {
                if (window.tomselectPenerimaEdit) window.tomselectPenerimaEdit.destroy();
                if (window.tomselectInstansiEdit) window.tomselectInstansiEdit.destroy();

                window.tomselectPenerimaEdit = new TomSelect('#edit-select-penerima', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    items: [@this.get('id_penerima')]
                });

                window.tomselectPenerimaEdit.on('change', (value) => {
                    @this.set('id_penerima', value);
                });

                window.tomselectInstansiEdit = new TomSelect('#edit-select-instansi', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    items: [@this.get('id_instansi')]
                });

                window.tomselectInstansiEdit.on('change', (value) => {
                    @this.set('id_instansi', value);
                });
            }

            Livewire.on('init-selects-for-edit', () => {
                setTimeout(() => {
                    initTomSelectEdit();
                }, 150);
            });

            Livewire.on('reset-tom-select-edit', () => {
                if (window.tomselectPenerimaEdit) window.tomselectPenerimaEdit.clear();
                if (window.tomselectInstansiEdit) window.tomselectInstansiEdit.clear();
            });
        </script>
    @endpush
</div>
