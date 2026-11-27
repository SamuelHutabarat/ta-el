<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Kopi TKP ({{ $periode['label'] }})</title>
    <!-- Bootstrap 5 CSS (needed for table formatting in print) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            padding: 20px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .report-header h2 {
            margin: 0;
            font-weight: bold;
            font-size: 20px;
        }
        .report-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
        }
        .meta-info {
            margin-bottom: 20px;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }
        th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <!-- Print Header -->
        <div class="report-header">
            <h2>KOPI TEMPAT KAMU PULANG (TKP)</h2>
            <p>Tempat Kamu Pulang Jl. Meruyung | Telp: (022) 1234567</p>
            <p class="fw-bold" style="font-size: 14px; margin-top: 10px; text-transform: uppercase;">Laporan Penjualan, Modal Belanja &amp; CRM Pelanggan</p>
        </div>

        <!-- Meta Info -->
        <div class="row meta-info">
            <div class="col-6">
                <strong>Periode {{ \App\Support\Periode::TIPE[$periode['tipe']] }}:</strong> {{ $periode['label'] }}
            </div>
            <div class="col-6 text-end">
                <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, H:i') }}<br>
                <strong>Dicetak Oleh:</strong> {{ Auth::user()->name }} (Owner)
            </div>
        </div>

        <!-- Sales Ledger -->
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal/Waktu</th>
                    <th>Nama Member</th>
                    <th>Kasir</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Total Akhir</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalHargaSum = 0;
                    $totalDiskonSum = 0;
                    $totalRevenueSum = 0;
                @endphp
                @forelse($transaksi as $key => $t)
                    @php
                        $totalHargaSum += $t->total_harga;
                        $totalDiskonSum += $t->diskon;
                        $totalRevenueSum += $t->total_akhir;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="fw-bold">{{ $t->kode_transaksi }}</td>
                        <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->pelanggan ? $t->pelanggan->nama . ' (' . $t->pelanggan->status_member . ')' : 'Guest Walk-in' }}</td>
                        <td>{{ $t->user ? $t->user->name : 'System' }}</td>
                        <td class="text-end">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($t->diskon, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $t->metode_pembayaran }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
                <tr class="fw-bold" style="background-color: #f9f9f9;">
                    <td colspan="5" class="text-end">JUMLAH TOTAL REKAPITULASI:</td>
                    <td class="text-end">Rp {{ number_format($totalHargaSum, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($totalDiskonSum, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($totalRevenueSum, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Rincian per hari / minggu -->
        @if($rincian->isNotEmpty())
            <table class="table table-bordered align-middle mt-4">
                <thead>
                    <tr>
                        <th>{{ $periode['tipe'] === 'mingguan' ? 'Hari' : 'Minggu' }}</th>
                        <th class="text-end">Transaksi</th>
                        <th class="text-end">Omset</th>
                        <th class="text-end">Modal Belanja</th>
                        <th class="text-end">Omset − Modal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rincian as $baris)
                        <tr>
                            <td>{{ $baris['label'] }}</td>
                            <td class="text-end">{{ $baris['transaksi'] }}</td>
                            <td class="text-end">Rp {{ number_format($baris['omset'], 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($baris['modal'], 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($baris['laba'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Ringkasan Keuangan -->
        <table class="table table-bordered align-middle mt-4" style="width: 60%; margin-left: auto;">
            <tr>
                <td>Omset (transaksi lunas)</td>
                <td class="text-end fw-bold">Rp {{ number_format($ringkasan['omset'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Diskon diberikan</td>
                <td class="text-end">Rp {{ number_format($ringkasan['diskon'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Modal belanja bahan</td>
                <td class="text-end">Rp {{ number_format($ringkasan['modal'], 0, ',', '.') }}</td>
            </tr>
            <tr class="fw-bold" style="background-color: #f9f9f9;">
                <td>Selisih omset − modal belanja</td>
                <td class="text-end">Rp {{ number_format($ringkasan['laba'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Signatures (Needed for formal student final exam thesis presentations) -->
        <div class="row mt-5 pt-4">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Bandung, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p style="margin-bottom: 70px;">Owner Kopi Tempat Kamu Pulang,</p>
                <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
                <small class="text-muted d-block" style="font-size: 0.75rem; font-style: italic;">Sistem CRM - Kopi TKP</small>
            </div>
        </div>

        <!-- Floating print actions -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print();" class="btn btn-primary px-4 py-2 me-2">Cetak Sekarang</button>
            <button onclick="window.close();" class="btn btn-secondary px-4 py-2">Tutup Halaman</button>
        </div>
    </div>

    <!-- Auto-Print script -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
