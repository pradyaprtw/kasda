<div>
   <div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form wire:submit.prevent="register">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" placeholder="Masukan Nama" class="form-control" id="name" wire:model="name" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="email">Email</label>
                        <input type="email" placeholder="Masukan Email" class="form-control" id="email" wire:model="email" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror   
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Kata Sandi</label>
                        <input type="password" placeholder="Masukan Kata Sandi" class="form-control" id="password" wire:model="password" required>
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror    
                    </div>
                    <div class="form-group mb-3 mt-3">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <input type="password" placeholder="Masukan Ulang Kata Sandi" class="form-control" id="password_confirmation" wire:model="password_confirmation" required>
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-primary">Daftar</button>
                        <a href="{{ route('login') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>
