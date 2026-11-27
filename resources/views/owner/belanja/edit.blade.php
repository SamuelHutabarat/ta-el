@extends('layouts.crm')

@section('title', 'Ubah Belanja Bahan - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.belanja-bahan.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Modal Belanja
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Ubah Belanja Bahan</h2>
        </div>
    </div>
    <div class="card-coffee">
        <div class="card-coffee-body">
            @include('owner.belanja._form', ['action' => route('owner.belanja-bahan.update', $belanja->id), 'method' => 'PUT'])
        </div>
    </div>
</div>
@endsection
