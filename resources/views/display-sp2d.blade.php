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

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-start">
                    <div class="mb-3">

                        <form action="{{ route('sp2d.export') }}" method="GET">
                            <h5>Export data SP2D</h5>
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Pilih tanggal</label>
                                <input type="date" class="form-control" id="created_at" name="created_at"
                                    style="border-color: black" required>
                            </div>
                            <button type="submit" class="btn btn-success"><i
                                    class="bi bi-cloud-arrow-down me-1"></i>Export</button>
                    </div>
                    </form>
                </div>
                {{-- <div class="d-flex justify-content-end">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSp2d">
                    <i class="bi bi-plus-circle me-1"></i> Tambah SP2D
                </button>
            </div> --}}
        </div>
    </div>
    <div class="mb-3">
        @livewire('CreateSp2d')
    </div>
    @livewire('sp2d')
    </div>
@endsection
