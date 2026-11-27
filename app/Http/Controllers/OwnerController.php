<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\Periode;
use App\Support\RingkasanPeriode;
use App\Support\Telepon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    /**
     * Display the owner dashboard with comprehensive statistics.
     */
    public function dashboard()
    {
        // 1. Core KPIs
        $totalSales = Transaksi::where('status_pembayaran', 'Lunas')->sum('total_akhir');
        $totalOrders = Transaksi::count();
        $totalMembers = Pelanggan::member()->count();
        $activePromos = Promosi::where('status', true)
            ->where('tanggal_selesai', '>=', Carbon::today())
            ->count();

        // 2. Sales last 7 days (for chart)
        $latestTrxDate = Transaksi::max('created_at');
        $hasRecentTrx = Transaksi::where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())->exists();

        // If today or last 7 days has activity, anchor to now.
        // Otherwise, if database has older transactions (e.g. from previous demo/seed date), anchor to latest transaction date
        // so the chart always displays real data and never flat zeros!
        $anchor = ($hasRecentTrx || !$latestTrxDate) ? Carbon::now() : Carbon::parse($latestTrxDate);
        $chartStartDate = $anchor->copy()->subDays(6)->startOfDay();
        $chartEndDate = $anchor->copy()->endOfDay();

        $salesChartData = Transaksi::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_akhir) as total')
        )
        ->whereBetween('created_at', [$chartStartDate, $chartEndDate])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        // Format chart labels and values
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayObj = $anchor->copy()->subDays($i);
            $day = $dayObj->format('Y-m-d');
            $formattedDay = $dayObj->isoFormat('D MMM');
            $chartLabels[] = $formattedDay;
            
            $found = $salesChartData->firstWhere('date', $day);
            $chartValues[] = $found ? (float)$found->total : 0;
        }

        $chartPeriod = $anchor->copy()->subDays(6)->isoFormat('D MMM Y') . ' - ' . $anchor->isoFormat('D MMM Y');

        // 3. Distribusi level member (Bronze, Silver, Gold)
        $memberLevels = Pelanggan::member()->select('status_member', DB::raw('count(*) as count'))
            ->groupBy('status_member')
            ->get();
        
        $levelLabels = ['Bronze', 'Silver', 'Gold'];
        $levelCounts = [0, 0, 0];
        foreach ($memberLevels as $ml) {
            $idx = array_search($ml->status_member, $levelLabels);
            if ($idx !== false) {
                $levelCounts[$idx] = $ml->count;
            }
        }

        // 4. Best Selling Products (Top 5)
        $bestSellers = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.produk_id', '=', 'produk.id')
            ->select('produk.nama_produk', DB::raw('SUM(detail_transaksi.jumlah) as total_qty'))
            ->groupBy('produk.id', 'produk.nama_produk')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        // 5. Recent Transactions (Top 5)
        $recentTransactions = Transaksi::with(['pelanggan', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $hariPenting = Pelanggan::hariPenting(Carbon::now(), 30);

        return view('owner.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalMembers',
            'activePromos',
            'chartLabels',
            'chartValues',
            'chartPeriod',
            'levelLabels',
            'levelCounts',
            'bestSellers',
            'recentTransactions',
            'hariPenting'
        ));
    }

    /**
     * User/Pengguna CRUD: List users.
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('owner.pengguna.index', compact('users'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        return view('owner.pengguna.create');
    }

    /**
     * Store new user. Akun pelanggan cukup nama + nomor telepon; owner & kasir wajib email.
     */
    public function store(Request $request)
    {
        $request->merge(['no_telp' => Telepon::normalisasi($request->input('no_telp'))]);

        $data = $request->validate($this->aturanPengguna($request, null) + [
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'no_telp' => $data['no_telp'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        // Akun pelanggan otomatis mendapat profil member; kasir & owner tidak pernah jadi member.
        if ($user->role === 'pelanggan') {
            Pelanggan::buatUntukAkun($user);
        }

        return redirect()->route('owner.pengguna.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show edit user form.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('owner.pengguna.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->merge(['no_telp' => Telepon::normalisasi($request->input('no_telp'))]);

        $data = $request->validate($this->aturanPengguna($request, $user) + [
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->no_telp = $data['no_telp'] ?? null;
        $user->role = $data['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if ($user->role === 'pelanggan') {
            $profil = $user->pelanggan ?? Pelanggan::buatUntukAkun($user);
            $profil->update([
                'nama' => $user->name,
                'no_telp' => $user->no_telp ?? $profil->no_telp,
            ]);
        } elseif ($user->pelanggan) {
            // Role bukan pelanggan: profil member dilepas.
            $user->pelanggan->delete();
        }

        return redirect()->route('owner.pengguna.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting logged in user
        if ($user->id === auth()->id()) {
            return redirect()->route('owner.pengguna.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('owner.pengguna.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Laporan penjualan per harian, mingguan, atau bulanan.
     */
    public function laporan(Request $request)
    {
        $periode = Periode::dariRequest($request, 'bulanan');
        $transaksi = $this->transaksiPeriode($periode);
        $ringkasan = RingkasanPeriode::hitung($periode['mulai'], $periode['selesai']);
        $rincian = RingkasanPeriode::rincian($periode['tipe'], $periode['mulai'], $periode['selesai']);

        return view('owner.laporan.index', compact('transaksi', 'periode', 'ringkasan', 'rincian'));
    }

    /**
     * Export sales report to CSV (dibuka di Excel).
     */
    public function exportExcel(Request $request)
    {
        $periode = Periode::dariRequest($request, 'bulanan');
        $transaksi = $this->transaksiPeriode($periode);
        $ringkasan = RingkasanPeriode::hitung($periode['mulai'], $periode['selesai']);

        $fileName = 'laporan-penjualan-kopi-tkp-'.$periode['tipe'].'-'
            .$periode['mulai']->format('Y-m-d').'-to-'.$periode['selesai']->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($transaksi, $periode, $ringkasan) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header information
            fputcsv($output, ['Laporan Penjualan Kopi Tempat Kamu Pulang (TKP)']);
            fputcsv($output, ['Periode:', Periode::TIPE[$periode['tipe']], $periode['label']]);
            fputcsv($output, ['Omset (Rp):', $ringkasan['omset']]);
            fputcsv($output, ['Diskon (Rp):', $ringkasan['diskon']]);
            fputcsv($output, ['Modal belanja bahan (Rp):', $ringkasan['modal']]);
            fputcsv($output, ['Selisih omset - modal (Rp):', $ringkasan['laba']]);
            fputcsv($output, []); // Empty row

            // Table headers
            fputcsv($output, [
                'Kode Transaksi',
                'Tanggal & Waktu',
                'Pelanggan',
                'Kasir',
                'Sumber',
                'Total Harga (Rp)',
                'Diskon (Rp)',
                'Total Akhir (Rp)',
                'Metode Pembayaran',
                'Status'
            ]);

            foreach ($transaksi as $t) {
                fputcsv($output, [
                    $t->kode_transaksi,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->pelanggan ? $t->pelanggan->nama : 'Guest Walk-in',
                    $t->user ? $t->user->name : 'System',
                    $t->sumber_transaksi === 'Online' ? 'Online' : 'Datang Langsung',
                    $t->total_harga,
                    $t->diskon,
                    $t->total_akhir,
                    $t->metode_pembayaran,
                    $t->status_pembayaran
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Print-friendly view for PDF generation.
     */
    public function exportPdf(Request $request)
    {
        $periode = Periode::dariRequest($request, 'bulanan');
        $transaksi = $this->transaksiPeriode($periode);
        $ringkasan = RingkasanPeriode::hitung($periode['mulai'], $periode['selesai']);
        $rincian = RingkasanPeriode::rincian($periode['tipe'], $periode['mulai'], $periode['selesai']);

        return view('owner.laporan.pdf', compact('transaksi', 'periode', 'ringkasan', 'rincian'));
    }

    /**
     * Transaksi lunas dalam rentang periode, terbaru di atas.
     */
    private function transaksiPeriode(array $periode)
    {
        return Transaksi::with(['pelanggan', 'user'])
            ->dalamPeriode($periode['mulai'], $periode['selesai'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Aturan validasi pengguna. Akun pelanggan: nomor telepon wajib (untuk login), email opsional.
     * Owner & kasir: email wajib, nomor telepon opsional.
     */
    private function aturanPengguna(Request $request, ?User $user): array
    {
        $abaikan = $user?->id;
        $pelanggan = $request->input('role') === 'pelanggan';

        return [
            'name' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['owner', 'kasir', 'pelanggan'])],
            'email' => $pelanggan
                ? ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($abaikan)]
                : ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($abaikan)],
            'no_telp' => $pelanggan
                ? ['required', 'string', 'max:20', Rule::unique('users', 'no_telp')->ignore($abaikan)]
                : ['nullable', 'string', 'max:20', Rule::unique('users', 'no_telp')->ignore($abaikan)],
        ];
    }
}
