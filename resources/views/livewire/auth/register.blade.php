<div>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Register</div>
                <div class="card-body">
                    <form wire:submit.prevent="register">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" placeholder="Masukan Nama" class="form-control" id="name"
                                wire:model="name" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="username">Username</label>
                            <input type="username" placeholder="Masukan username" class="form-control" id="username"
                                wire:model="username" required>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="password">Kata Sandi</label>
                            <input type="password" placeholder="Masukan Kata Sandi" class="form-control" id="password"
                                wire:model="password" required>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3 mt-3">
                            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                            <input type="password" placeholder="Masukan Ulang Kata Sandi" class="form-control"
                                id="password_confirmation" wire:model="password_confirmation" required>
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <d class="mb-3 mt-3 text-center">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="register">Tambah</span>
                                <span wire:loading wire:target="register">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Memuat...
                                </span>
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
