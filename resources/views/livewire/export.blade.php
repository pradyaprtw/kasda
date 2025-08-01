    <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white text-center rounded-top-3">
            <h3 class="mb-0">Export Data SP2D</h3>
        </div>
        <div class="card-body">
            <p class="text-center fw-bold mb-4">Silakan pilih jenis export di bawah ini</p>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                {{-- Export Harian --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.harian') }}" method="GET">
                            <h5 class="mb-3">📅 Export Harian</h5>
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control mb-3 border-dark" name="created_at"
                                value="{{ \Carbon\Carbon::today()->toDateString() }}" readonly required>
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Harian
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Export Mingguan --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.mingguan') }}" method="GET">
                            <h5 class="mb-3">📆 Export Mingguan</h5>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label for="minggu" class="form-label">Minggu ke-</label>
                                    <select name="minggu" id="minggu" class="form-select border-dark" required>
                                        <option value="">Pilih Minggu Ke-</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="bulan_minggu" class="form-label">Pilih Bulan</label>
                                    <select name="bulan" id="bulan_minggu" class="form-select border-dark" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach (range(1, 12) as $b)
                                            <option value="{{ $b }}">
                                                {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="tahun_minggu" class="form-label">Pilih Tahun</label>
                                    <select name="tahun" id="tahun_minggu" class="form-select border-dark" required>
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = now()->year; $i >= 2020; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-success w-100 mt-3">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Mingguan
                            </button>
                        </form>
                    </div>
                </div>


                {{-- Export Bulanan --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.bulanan') }}" method="GET">
                            <h5 class="mb-3">🗓️ Export Bulanan</h5>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="bulan" class="form-label">Pilih Bulan</label>
                                    <select name="bulan" id="bulan" class="form-select border-dark" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach (range(1, 12) as $b)
                                            <option value="{{ $b }}">
                                                {{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="tahun" class="form-label">Pilih Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select border-dark" required>
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = now()->year; $i >= 2020; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-success w-100 mt-3">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Bulanan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Export Tahunan --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.tahunan') }}" method="GET">
                            <h5 class="mb-3">📊 Export Tahunan</h5>
                            <label class="form-label">Pilih Tahun</label>
                            <select name="tahun" class="form-select border-dark" required>
                                <option value="">Pilih Tahun</option>
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-outline-success w-100 mt-3">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Tahunan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Export Gaji --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.gaji') }}" method="GET">
                            <h5 class="mb-3">📅 Export Gaji</h5>
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control mb-3 border-dark" name="created_at" required>
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Gaji
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Export Pajak --}}
                <div class="col">
                    <div class="p-4 border rounded-3 h-100 shadow-sm">
                        <form action="{{ route('export.pajak') }}" method="GET">
                            <h5 class="mb-3">📅 Export Pajak</h5>
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control mb-3 border-dark" name="created_at" required>
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-cloud-arrow-down me-1"></i> Export Pajak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
