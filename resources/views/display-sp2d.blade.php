@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        {{-- Success/Error Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <div class="mb-3">
                <form action="{{ route('sp2d.export') }}" method="GET">
                    <h5>Export harian SP2D</h5>
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Pilih tanggal</label>
                        <input type="date" class="form-control" id="created_at" name="created_at" style="border-color: black" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-cloud-arrow-down me-1"></i>Export
                    </button>
                </form>
            </div>

            <div class="mb-3">
                <form action="{{ route('sp2d.rekap.export') }}" method="GET">
                    <h5>Export Rekap SP2D</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" style="border-color: black" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" style="border-color: black" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="bi bi-cloud-arrow-down me-1"></i>Export
                    </button>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
            </div>
        </div>
        <div class="mb-3">
            @livewire('CreateSp2d')
        </div>
        @livewire('sp2d')
    </div>
@endsection
