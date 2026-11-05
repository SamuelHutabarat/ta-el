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
        $salesChartData = Transaksi::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_akhir) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        // Format chart labels and values
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $formattedDay = Carbon::now()->subDays($i)->isoFormat('D MMM');
            $chartLabels[] = $formattedDay;
            
            $found = $salesChartData->firstWhere('date', $day);
            $chartValues[] = $found ? (float)$found->total : 0;
        }

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
     * Export sales report to Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $fileName = "laporan-penjualan-kopi-tkp-" . $startDate . "-to-" . $endDate . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "Laporan Penjualan Kopi Tempat Kamu Pulang (TKP)\n";
        echo "Periode: " . $startDate . " s/d " . $endDate . "\n\n";

        echo "Kode Transaksi\tTanggal\tPelanggan\tKasir/Barista\tTotal Harga\tDiskon\tTotal Akhir\tMetode Pembayaran\n";

        foreach ($transaksi as $t) {
            $pelangganNama = $t->pelanggan ? $t->pelanggan->nama : 'Guest';
            $baristaNama = $t->user ? $t->user->name : 'System';
            
            // Format numbers
            $totalHarga = number_format($t->total_harga, 0, ',', '');
            $diskon = number_format($t->diskon, 0, ',', '');
            $totalAkhir = number_format($t->total_akhir, 0, ',', '');

            echo "{$t->kode_transaksi}\t{$t->created_at}\t{$pelangganNama}\t{$baristaNama}\t{$totalHarga}\t{$diskon}\t{$totalAkhir}\t{$t->metode_pembayaran}\n";
        }
        exit;
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
