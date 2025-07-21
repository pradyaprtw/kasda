@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-end">
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createPenerima">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Penerima
                </button>
            </div>
            @livewire('Penerima')
        </div>
    </div>
@livewire('CreatePenerima')
</div>
@endsection