<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\Promosi;
use App\Models\LoyalitasPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;

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
        $totalMembers = Pelanggan::count();
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

        // 3. Loyalty Member Distribution (Bronze, Silver, Gold)
        $memberLevels = Pelanggan::select('status_member', DB::raw('count(*) as count'))
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
            'recentTransactions'
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
     * Store new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:owner,barista,pelanggan',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // If the role created is a customer (pelanggan), auto-create profile
        if ($user->role === 'pelanggan') {
            $latestPelanggan = Pelanggan::orderBy('id', 'desc')->first();
            $nextNum = $latestPelanggan ? ((int)substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;
            $kode = 'PLG-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $pelanggan = Pelanggan::create([
                'user_id' => $user->id,
                'kode_pelanggan' => $kode,
                'nama' => $user->name,
                'no_telp' => '-',
                'alamat' => '-',
                'poin' => 0,
                'status_member' => 'Bronze',
            ]);

            LoyalitasPelanggan::create([
                'pelanggan_id' => $pelanggan->id,
                'total_akumulasi_transaksi' => 0,
                'level_member' => 'Bronze',
                'diskon_khusus' => 0,
                'tanggal_bergabung' => Carbon::now(),
            ]);
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:owner,barista,pelanggan',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sync pelanggan profile name if exists
        if ($user->role === 'pelanggan' && $user->pelanggan) {
            $user->pelanggan->nama = $user->name;
            $user->pelanggan->save();
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
     * Sales reports filtering view.
     */
    public function laporan(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalHarga = $transaksi->sum('total_harga');
        $totalDiskon = $transaksi->sum('diskon');
        $totalRevenue = $transaksi->sum('total_akhir');

        return view('owner.laporan.index', compact('transaksi', 'startDate', 'endDate', 'totalHarga', 'totalDiskon', 'totalRevenue'));
    }

    /**
     * Export sales report to Excel/CSV.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $fileName = "laporan-penjualan-kopi-tkp-" . $startDate . "-to-" . $endDate . ".csv";

        return response()->streamDownload(function () use ($transaksi, $startDate, $endDate) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header information
            fputcsv($output, ['Laporan Penjualan Kopi Tempat Kamu Pulang (TKP)']);
            fputcsv($output, ['Periode:', $startDate . ' s/d ' . $endDate]);
            fputcsv($output, []); // Empty row

            // Table headers
            fputcsv($output, [
                'Kode Transaksi',
                'Tanggal & Waktu',
                'Pelanggan',
                'Kasir / Barista',
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
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.laporan.pdf', compact('transaksi', 'startDate', 'endDate'));
    }

    /**
     * Monitoring loyalty programs & member profiles.
     */
    public function loyalitas()
    {
        $loyalitas = LoyalitasPelanggan::with('pelanggan')
            ->orderBy('total_akumulasi_transaksi', 'desc')
            ->get();

        return view('owner.loyalitas.index', compact('loyalitas'));
    }
}
