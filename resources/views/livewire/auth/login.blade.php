<div>
    <div class="row justify-content-center">
     <div class="col-md-4">
          <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                     <form wire:submit.prevent="login">
                          <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" placeholder="Masukan Email" class="form-control" id="email" wire:model="email" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror   
                          </div>
                          <div class="form-group mt-3">
                                <label for="password">Kata Sandi</label>
                                <input type="password" placeholder="Masukan Kata Sandi" class="form-control" id="password" wire:model="password" required>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror    
                          </div>
                          <div class="mb-3 mt-3 text-center">
                                <button type="submit" class="btn btn-primary">Masuk</button>
                          </div>
                     </form>
                     <a href="{{ route('register') }}" class="text-primary text-center">Tambah Akun</a>
                </div>
          </div>
     </div>
    </div>
</div>
