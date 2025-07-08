@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12"> <!-- Dibuat lebih lebar agar muat -->
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5 class="mb-3">Selamat Datang, {{ Auth::user()->name }}!</h5>

                    <!-- MENAMPILKAN REKAP BULANAN -->
                    <hr>
                    <h6 class="mt-4 mb-3">Rekapitulasi Bulanan</h6>
                    <div class="row">
                        <!-- Card Netto Bulan Ini -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Netto Bulan Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp {{ number_format($rekapBulanan['nettoBulanIni'], 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Netto Bulan Lalu -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Netto Bulan Lalu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp {{ number_format($rekapBulanan['nettoBulanLalu'], 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Total PFK -->
                       <div class="col-lg-3 col-md-6 mb-4">
                           <div class="card border-left-warning shadow h-100 py-2">
                               <div class="card-body">
                                   <div class="row no-gutters align-items-center">
                                       <div class="col mr-2">
                                           <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total PFK Bulan Ini</div>
                                           <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPfk'], 2, ',', '.') }}</div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                        <!-- Card Total Bruto Hari Ini -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bruto Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['brutoSemua'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                         <!-- Card Total PPN -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">Total PPN Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPPN'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Card Total PPH 21-->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">Total PPH 21 Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPPH21'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Card Total PPH 22-->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">Total PPH 22 Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPPH22'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Card Total PPH 23 -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">Total PPH 23 Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPPH23'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Card Total PPH 4-->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">Total PPH 4 Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['totalPPH4'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Total Netto Hari Ini -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Netto Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['nettoHariIni'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                         <!-- Card Rekap Akhir -->
                        <div class="col-lg-3 col-md-6 mb-4">
                             @php
                                $rekapColor = $rekapBulanan['rekapAkhir'] >= 0 ? 'success' : 'danger';
                            @endphp
                            <div class="card border-left-{{ $rekapColor }} shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-{{ $rekapColor }} text-uppercase mb-1">Rekap Akhir</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['rekapAkhir'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Total Netto Keseluruhan --}}
                        <div class="col-lg-3 col-md-6 mb-4">
                             @php
                                $rekapColor = $rekapBulanan['nettoSemua'] >= 0 ? 'info' : 'danger';
                            @endphp
                            <div class="card border-left-{{ $rekapColor }} shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-{{ $rekapColor }} text-uppercase mb-1">Total Netto Keseluruhan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rekapBulanan['nettoSemua'], 2, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- MENAMPILKAN INPUT PETUGAS HARI INI -->
                    <hr>
                    <div class="card-body">
                        <h5 class="card-title">Aktivitas Petugas Hari Ini</h5>
                        <ul class="list-group list-group-flush">
                            @forelse ($petugasStats as $stats)
                                <li class="list-group-item">
                                    <strong>{{ $stats->name }}</strong> menambah data sebanyak <strong>{{ $stats->total_input_hari_ini }}</strong> kali.
                                </li>
                                
                            @empty
                                <li class="list-group-item">Belum ada aktivitas input data hari ini.</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection