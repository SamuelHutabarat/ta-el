@extends('layouts.crm')

@title('Laporan Penjualan - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Laporan Penjualan & Keuangan</h2>
            <p class="text-muted-coffee">Filter dan analisa omset penjualan, loyalitas pelanggan, dan cetak dokumen laporan keuangan.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card-coffee mb-4">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-filter me-2 text-warning"></i>Filter Periode Laporan</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="start_date" class="form-label form-label-coffee">Tanggal Mulai</label>
                    <input type="date" class="form-control form-control-coffee" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-4">
                    <label for="end_date" class="form-label form-label-coffee">Tanggal Selesai</label>
                    <input type="date" class="form-control form-control-coffee" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-4 d-flex">
                    <button type="submit" class="btn btn-gold w-100 me-2">
                        <i class="fa-solid fa-sync me-2"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('owner.laporan.index') }}" class="btn btn-outline-gold px-3" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics Row -->
    <div class="row g-4 mb-4">
        <!-- Total Gross -->
        <div class="col-12 col-md-4">
            <div class="card-coffee border-info">
                <div class="card-coffee-body text-center py-4">
                    <span class="text-muted-coffee d-block mb-1 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Kotor (Subtotal)</span>
                    <h3 class="fw-bold text-info m-0">Rp {{ number_format($totalHarga, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Diskon -->
        <div class="col-12 col-md-4">
            <div class="card-coffee border-danger">
                <div class="card-coffee-body text-center py-4">
                    <span class="text-muted-coffee d-block mb-1 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Total Potongan Diskon</span>
                    <h3 class="fw-bold text-danger m-0">-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Revenue Net -->
        <div class="col-12 col-md-4">
            <div class="card-coffee border-success" style="background: linear-gradient(135deg, rgba(44, 27, 24, 0.5) 0%, rgba(20, 12, 11, 0.8) 100%);">
                <div class="card-coffee-body text-center py-4">
                    <span class="text-muted-coffee d-block mb-1 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Pendapatan Bersih (Net)</span>
                    <h3 class="fw-bold text-warning m-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons & Table Card -->
    <div class="card-coffee">
        <div class="card-coffee-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-file-invoice-dollar me-2 text-warning"></i>Data Penjualan Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h5>
            
            <div class="d-flex gap-2">
                <!-- Export to Excel -->
                <a href="{{ route('owner.laporan.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success px-3 py-1.5 fw-bold">
                    <i class="fa-regular fa-file-excel me-1"></i> Export Excel
                </a>
                
                <!-- Export/Print PDF -->
                <a href="{{ route('owner.laporan.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-sm btn-gold px-3 py-1.5 fw-bold">
                    <i class="fa-regular fa-file-pdf me-1"></i> Cetak PDF / Nota
                </a>
            </div>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="laporanTable">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal / Jam</th>
                            <th>Nama Pelanggan</th>
                            <th>Petugas / Barista</th>
                            <th>Metode Bayar</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Diskon</th>
                            <th class="text-end">Total Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $t)
                            <tr>
                                <td class="fw-bold text-warning">{{ $t->kode_transaksi }}</td>
                                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($t->pelanggan)
                                        <span class="fw-bold text-light">{{ $t->pelanggan->nama }}</span>
                                        <span class="badge-member badge-bronze ms-1 py-0.5 px-2" style="font-size: 0.65rem;">{{ $t->pelanggan->status_member }}</span>
                                    @else
                                        <span class="text-muted">Guest (Walk-in)</span>
                                    @endif
                                </td>
                                <td>{{ $t->user ? $t->user->name : 'System' }}</td>
                                <td>
                                    <span class="badge bg-opacity-25 {{ $t->metode_pembayaran === 'QRIS' ? 'bg-info text-info' : 'bg-success text-success' }} px-2 py-0.5" style="font-size: 0.75rem;">
                                        {{ $t->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="text-end">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">-Rp {{ number_format($t->diskon, 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted-coffee py-5">
                                    <i class="fa-regular fa-folder-open fs-2 d-block mb-2 text-warning"></i> Tidak ditemukan transaksi pada periode tanggal terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transaksi->isNotEmpty())
                        <tfoot>
                            <tr class="fw-bold border-top" style="border-color: var(--accent-gold) !important; background-color: rgba(0, 0, 0, 0.25);">
                                <td colspan="5" class="text-end text-warning">JUMLAH TOTAL:</td>
                                <td class="text-end text-info">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                                <td class="text-end text-success fs-5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#laporanTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "order": [[1, "desc"]],
            "responsive": true,
            "searching": false, // filter inputs handled by our dates form
            "paging": true
        });
    });
</script>
@endsection
