@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif
        {{-- NOTIFIKASI BARU: INFORMASI PENGHAPUSAN DATA --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($cleanupFlag)
            <div class="alert alert-warning">
                <strong>⚠️ Data SP2D lebih dari 5 tahun siap dihapus sejak
                    {{ \Carbon\Carbon::parse($cleanupFlag->tanggal_trigger)->format('d M Y') }} ⚠️</strong><br>
                Apakah Anda ingin menghapusnya sekarang?
                <div class="mt-2">
                    <form action="{{ route('konfirmasi.hapus') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="flag_id" value="{{ $cleanupFlag->id }}">
                        <button type="submit" name="action" value="yes" class="btn btn-danger btn-sm">Ya,
                            hapus</button>
                        <button type="submit" name="action" value="later" class="btn btn-secondary btn-sm">Ingatkan
                            Besok</button>
                    </form>
                </div>
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <!-- WELCOME MESSAGE -->
                        <h5 class="mb-4 text-center fw-bold" style="font-size: 22px;">
                            Selamat Datang, <span class="text-primary">{{ Auth::user()->name }}</span>!
                        </h5>

                        <!-- RINGKASAN HARI INI -->
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Hari ini total transaksi <strong>Rp
                                {{ number_format($rekapBulanan['nettoHariIni'], 2, ',', '.') }}</strong>
                            dari semua jenis SP2D.
                        </div>

                        <!-- RINGKASAN UTAMA -->
                        <h6 class="fw-bold text-center mt-4 mb-3">Ringkasan Utama</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-wallet2 text-primary" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-1 mt-2">Netto Bulan Ini</p>
                                        <h5 class="fw-bold text-primary">
                                            Rp {{ number_format($rekapBulanan['nettoBulanIni'], 2, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-calendar-week text-secondary" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-1 mt-2">Netto Bulan Lalu</p>
                                        <h5 class="fw-bold text-secondary">
                                            Rp {{ number_format($rekapBulanan['nettoBulanLalu'], 2, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bank text-warning" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-1 mt-2">Total PFK Bulan Ini</p>
                                        <h5 class="fw-bold text-secondary">
                                            Rp {{ number_format($rekapBulanan['totalPfk'], 2, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-graph-up text-success" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-1 mt-2">Rekap Akhir
                                            <br>
                                            <small>ket: netto bulan ini - bulan lalu - total pfk bulan ini</small>
                                        </p>
                                        <h5 class="fw-bold text-success">
                                            Rp {{ number_format($rekapBulanan['rekapAkhir'], 2, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DETAIL PAJAK & BRUTO -->
                        <!--
                                                    Bagian ini menampilkan detail-data lainnya yang relevan
                                                    dengan transaksi SP2D. Data-data tersebut diambil dari
                                                    variabel $rekapBulanan yang dihitung di controller.

                                                    Data-data yang ditampilkan adalah:
                                                    1. Bruto hari ini
                                                    2. Total PPN hari ini
                                                    3. Total PPH 21 hari ini
                                                    4. Total PPH 22 hari ini
                                                    5. Total PPH 23 hari ini
                                                    6. Total PPH 4 hari ini
                                                    7. Netto hari ini
                                                    8. Total netto keseluruhan
                                                -->
                        <h6 class="fw-bold text-center mt-5 mb-3">Detail Lainnya</h6>
                        <div class="row g-3">
                            @php
                                $detailItems = [
                                    [
                                        'label' => 'Bruto Hari Ini',
                                        'value' => $rekapBulanan['brutoSemua'],
                                        'color' => 'danger',
                                        'icon' => 'bi-cash-stack',
                                    ],
                                    [
                                        'label' => 'Total PPN Hari Ini',
                                        'value' => $rekapBulanan['totalPPN'],
                                        'color' => 'info',
                                        'icon' => 'bi-percent',
                                    ],
                                    [
                                        'label' => 'Total PPH 21 Hari Ini',
                                        'value' => $rekapBulanan['totalPPH21'],
                                        'color' => 'info',
                                        'icon' => 'bi-percent',
                                    ],
                                    [
                                        'label' => 'Total PPH 22 Hari Ini',
                                        'value' => $rekapBulanan['totalPPH22'],
                                        'color' => 'info',
                                        'icon' => 'bi-percent',
                                    ],
                                    [
                                        'label' => 'Total PPH 23 Hari Ini',
                                        'value' => $rekapBulanan['totalPPH23'],
                                        'color' => 'info',
                                        'icon' => 'bi-percent',
                                    ],
                                    [
                                        'label' => 'Total PPH 4 Hari Ini',
                                        'value' => $rekapBulanan['totalPPH4'],
                                        'color' => 'info',
                                        'icon' => 'bi-percent',
                                    ],
                                    [
                                        'label' => 'Netto Hari Ini',
                                        'value' => $rekapBulanan['nettoHariIni'],
                                        'color' => 'primary',
                                        'icon' => 'bi-currency-exchange',
                                    ],
                                    [
                                        'label' => 'Total Netto Keseluruhan',
                                        'value' => $rekapBulanan['nettoSemua'],
                                        'color' => 'success',
                                        'icon' => 'bi-collection',
                                    ],
                                ];
                            @endphp

                            @foreach ($detailItems as $item)
                                <div class="col-lg-3 col-md-6">
                                    <div class="card shadow-sm h-100 border-left-{{ $item['color'] }}">
                                        <div class="card-body text-center">
                                            <i class="bi {{ $item['icon'] }} text-{{ $item['color'] }}"
                                                style="font-size: 1.8rem;"></i>
                                            <p class="text-muted mb-1 mt-2">{{ $item['label'] }}</p>
                                            <h6 class="fw-bold text-gray-800">
                                                Rp {{ number_format($item['value'], 2, ',', '.') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- AKTIVITAS PETUGAS -->
                        <h6 class="fw-bold text-center mt-5 mb-3">Aktivitas Petugas Hari Ini</h6>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @forelse ($petugasStats as $stats)
                                        <li class="list-group-item">
                                            <i class="bi bi-person-fill me-2"></i>
                                            <strong>{{ $stats->name }}</strong> menambah data sebanyak
                                            <strong>{{ $stats->total_input_hari_ini }}</strong> kali.
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center">Belum ada aktivitas input data hari ini.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
