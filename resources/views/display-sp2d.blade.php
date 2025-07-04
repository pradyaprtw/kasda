@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
