@extends('layouts.crm')

@section('title', 'Kritik & Saran - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kritik &amp; Saran Pelanggan</h2>
            <p class="text-muted-coffee">Masukan dari member untuk bahan perbaikan menu dan layanan.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-comment-dots me-2 text-warning"></i>Daftar Masukan ({{ $daftar->count() }})</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="kritikTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Pengirim</th>
                            <th>Isi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftar as $k)
                            <tr>
                                <td style="white-space: nowrap;">{{ $k->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($k->jenis === 'Kritik')
                                        <span class="badge bg-danger">Kritik</span>
                                    @else
                                        <span class="badge bg-info text-dark">Saran</span>
                                    @endif
                                </td>
                                <td>
                                    @if($k->pelanggan)
                                        <div class="fw-bold text-light">{{ $k->pelanggan->nama }}</div>
                                        <small class="text-warning">{{ $k->pelanggan->kode_pelanggan }}</small>
                                    @else
                                        <span class="text-muted">Tamu</span>
                                    @endif
                                </td>
                                <td style="min-width: 260px;">{{ $k->isi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
