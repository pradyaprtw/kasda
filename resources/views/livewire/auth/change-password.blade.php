<div>
    @if ($show)
        <div class="modal fade show" style="display: block;" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lupa Kata Sandi</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">

                        @if ($success)
                            <div class="alert alert-success">
                                Password berhasil diubah! Silakan tutup jendela ini dan coba login kembali.
                            </div>
                        @elseif ($step === 1)
                            {{-- Langkah 1: Masukkan Username --}}
                            <div>
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="username" wire:model="username"
                                    wire:keydown.enter="checkUsername" placeholder="Masukan Username">
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <br>
                                <button wire:click="checkUsername" class="btn btn-primary mt-3"
                                    wire:loading.attr="disabled" wire:target="checkUsername">
                                    <span wire:loading.remove wire:target="checkUsername">Lanjut</span>
                                    <span wire:loading wire:target="checkUsername">Mencari...</span>
                                </button>
                            </div>
                        @elseif ($step === 2)
                            {{-- Langkah 2: Masukkan Password Baru --}}
                            <div>
                                <div class="alert alert-info p-2">
                                    Pengguna ditemukan: <strong>{{ $foundUser->name }}</strong>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru:</label>
                                    <input type="password" id="new_password" class="form-control"
                                        wire:model="new_password" placeholder="Masukkan Password Baru">
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi
                                        Password:</label>
                                    <input type="password" id="new_password_confirmation" class="form-control"
                                        wire:model="new_password_confirmation" placeholder="Konfirmasi Password Baru">
                                    @error('new_password')
                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button wire:click="resetPassword" class="btn btn-success mt-3"
                                    wire:loading.attr="disabled" wire:target="resetPassword">
                                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                                    <span wire:loading wire:target="resetPassword">Menyimpan...</span>
                                </button>
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary " wire:click.prevent="closeModal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
