<div class="vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header text-center fw-bold">Login</div>
            <div class="card-body">
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form wire:submit.prevent="login">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" placeholder="Masukan username" class="form-control" id="username"
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
                <div class="d-flex justify-content-between">
                   <div class="mb-3">
                        <a href="{{ route('register') }}" class="btn btn-link">Tambah Akun</a>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-link" wire:click="$dispatch('openForgotPasswordModal')">
                            Lupa Kata Sandi?
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewire('auth.change-password')
</div>
