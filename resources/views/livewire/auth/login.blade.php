<div>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form wire:submit.prevent="login">
                        <div class="form-group">
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
                        <div class="mb-3 mt-3 text-center">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="login">Masuk</span>
                                <span wire:loading wire:target="login">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Memuat...
                                </span>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('register') }}" class="text-primary text-center">Tambah Akun</a>
                </div>
            </div>
        </div>
    </div>
</div>
