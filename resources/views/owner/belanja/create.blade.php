@extends('layouts.crm')

@section('title', 'Catat Belanja Bahan - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.belanja-bahan.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Modal Belanja
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Catat Belanja Bahan</h2>
            <p class="text-muted-coffee">Setiap belanja bahan tercatat sebagai modal, dihitung per minggu dan per bulan.</p>
        </div>
    </div>
    <div class="card-coffee">
        <div class="card-coffee-body">
            @include('owner.belanja._form', ['action' => route('owner.belanja-bahan.store'), 'method' => 'POST'])
        </div>
    </div>
</div>
@endsection
