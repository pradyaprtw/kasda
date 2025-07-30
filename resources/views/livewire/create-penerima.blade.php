<div class="modal fade" id="createPenerima" tabindex="-1" aria-labelledby="createPenerimaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createPenerimaLabel">Tambah Penerima</h1>
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
                        <label for="nama_penerima" class="form-label">Nama CV/Penerima</label>
                        <input type="text" class="form-control" id="nama_penerima" wire:model="nama_penerima"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="no_rek" class="form-label">No Rekening</label>
                        <input type="text" class="form-control" id="no_rek" wire:model="no_rek"
                            required>
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
                    </div>                </form>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div> --}}
        </div>
    </div>
</div>
