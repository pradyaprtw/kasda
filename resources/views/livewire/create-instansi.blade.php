<div class="modal fade" id="createInstansi" tabindex="-1" aria-labelledby="createInstansiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createInstansiLabel">Tambah Sp2d</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Display success message --}}
        @if (session()->has('message'))
                <div class="alert alert-success mt-3 mx-3">
                        {{ session('message') }}
                </div>
        @endif
            <div class="modal-body">
                <form wire:submit.prevent="store">
                        <div class="mb-3">
                                <label for="nama_instansi" class="form-label">Nama Instansi</label>
                                <input type="text" class="form-control" id="nama_instansi" wire:model="nama_instansi" required>
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
</div>
