@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-start">
                <form action="{{ route('sp2d.export') }}" method="GET">
                    <h5>Export data SP2D</h5>
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Pilih tanggal</label>
                        <input type="date" class="form-control" id="created_at" name="created_at" style="border-color: black" required>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-arrow-down me-1"></i>Export</button>
                </form>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSp2d">
                    <i class="bi bi-plus-circle me-1"></i> Tambah SP2D
                </button>
            </div>
            @livewire('sp2d')
        </div>
    </div>
@livewire('CreateSp2d')
</div>
@endsection
