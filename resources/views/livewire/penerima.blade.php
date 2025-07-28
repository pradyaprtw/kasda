<div>
    {{-- Alert Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Pencarian --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    placeholder="Cari penerima...">
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama CV/Penerima</th>
                        <th scope="col">No Rekening</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penerima as $index => $p)
                        <tr>
                            <th scope="row" class="text-center">{{ $penerima->firstItem() + $index }}</th>
                            <td>{{ $p->nama_penerima }}</td>
                            <td>{{ $p->no_rek }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm me-1"
                                    wire:click="edit({{ $p->id }})" wire:target="edit({{ $p->id }})">
                                    <span wire:loading.remove wire:target="edit({{ $p->id }})">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </span>
                                    <span wire:loading wire:target="edit({{ $p->id }})">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="delete({{ $p->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus data ini?"
                                    wire:loading.attr="disabled" wire:target="delete({{ $p->id }})">
                                    <span wire:loading.remove wire:target="delete({{ $p->id }})">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </span>
                                    <span wire:loading wire:target="delete({{ $p->id }})">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
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
        {{ $penerima->links() }}
    </div>

    {{-- =================== MODAL EDIT ================= --}}
    @if ($showEditModal)
        <div class="modal fade show d-block" id="editPenerima" tabindex="-1" aria-labelledby="editPenerimaLabel"
            aria-hidden="true" style="background: transparent;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editPenerimaLabel">Edit Penerima</h1>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Display success message --}}
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="update">
                            <div class="mb-3">
                                <label for="nama_penerima" class="form-label">Nama CV/Penerima</label>
                                <input type="text" class="form-control @error('nama_penerima') is-invalid @enderror"
                                    id="nama_penerima" wire:model="nama_penerima" required>
                                @error('nama_penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-3 d-flex justify-content-end">
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
</div>
