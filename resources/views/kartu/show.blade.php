@extends('layouts.crm')

@section('title', 'Kartu Member - ' . $pelanggan->nama)

@section('styles')
<style>
    .kartu-wrap { display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; gap: 1.5rem; }
    .kartu-area {
        width: 340px; height: 214px; border-radius: 16px; padding: 18px 20px; position: relative; overflow: hidden;
        color: #FAF5F2; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .kartu-area.tier-gold { background: linear-gradient(135deg, #D4A047, #7A4E12 60%, #2D1E1B); }
    .kartu-area.tier-silver { background: linear-gradient(135deg, #DDE2EA, #6E7885 60%, #2D1E1B); color: #1f1f1f; }
    .kartu-area.tier-bronze { background: linear-gradient(135deg, #C98B5A, #6B3F22 60%, #2D1E1B); }
    .kartu-brand { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.1rem; }
    .kartu-brand small { display: block; font-family: 'Outfit', sans-serif; font-weight: 400; font-size: 0.6rem; letter-spacing: 2px; text-transform: uppercase; opacity: 0.85; }
    .kartu-kode { font-family: monospace; font-size: 1.05rem; letter-spacing: 3px; margin-top: 20px; }
    .kartu-nama { font-weight: 700; font-size: 1.15rem; margin-top: 2px; }
    .kartu-meta { position: absolute; left: 20px; bottom: 16px; font-size: 0.72rem; line-height: 1.5; }
    .kartu-poin { position: absolute; right: 20px; bottom: 14px; text-align: right; }
    .kartu-poin .label { font-size: 0.6rem; letter-spacing: 2px; text-transform: uppercase; }
    .kartu-poin .angka { font-size: 2.1rem; font-weight: 800; line-height: 1; }
    @media print {
        body * { visibility: hidden !important; }
        .kartu-area, .kartu-area * { visibility: visible !important; }
        .kartu-area { position: absolute; left: 0; top: 0; box-shadow: none; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 no-print">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kartu Member</h2>
                <p class="text-muted-coffee mb-0">Kartu ini menampilkan level dan poin reward. Klik Cetak untuk mencetak kartu.</p>
            </div>
            <div>
                <a href="{{ $kembali }}" class="btn btn-outline-gold me-2">Kembali</a>
                <button type="button" class="btn btn-gold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> Cetak Kartu</button>
            </div>
        </div>
    </div>

    <div class="kartu-wrap">
        <div class="kartu-area tier-{{ strtolower($pelanggan->status_member) }}">
            <div class="kartu-brand">Kopi TKP<small>Tempat Kamu Pulang · Member CRM</small></div>
            <div class="kartu-kode">{{ $pelanggan->kode_pelanggan }}</div>
            <div class="kartu-nama">{{ $pelanggan->nama }}</div>
            <div class="kartu-meta">
                Level: <strong>{{ $pelanggan->status_member }}</strong> · Diskon {{ $pelanggan->diskonPersen() }}%<br>
                Bergabung: {{ $pelanggan->tanggal_bergabung?->locale('id')->isoFormat('D MMM Y') ?? '-' }}
            </div>
            <div class="kartu-poin">
                <div class="label">Poin Reward</div>
                <div class="angka">{{ $pelanggan->poin }}</div>
            </div>
        </div>

        <div class="card-coffee no-print" style="width: 340px;">
            <div class="card-coffee-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted-coffee">Poin reward</span><strong class="text-warning">{{ $pelanggan->poin }} poin</strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted-coffee">Total belanja</span><strong>Rp {{ number_format((float) $pelanggan->total_belanja, 0, ',', '.') }}</strong></div>
                <div class="d-flex justify-content-between mb-3"><span class="text-muted-coffee">No. telepon</span><strong>{{ $pelanggan->no_telp }}</strong></div>
                <small class="text-muted-coffee d-block">
                    Poin: 1 poin setiap Rp {{ number_format(config('crm.rupiah_per_poin.Offline'), 0, ',', '.') }} belanja datang langsung,
                    dan 1 poin setiap Rp {{ number_format(config('crm.rupiah_per_poin.Online'), 0, ',', '.') }} untuk pesanan online.
                    Tukar 50 poin = potongan Rp 22.000.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
