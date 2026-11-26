<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LoyalitasPelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Owner, Barista, Pelanggan)
        $ownerUser = User::create([
            'name' => 'Owner TKP',
            'email' => 'owner@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $baristaUser = User::create([
            'name' => 'Andi Barista',
            'email' => 'barista@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'barista',
        ]);

        // Gold Member 1 (As documented in README.md)
        $budiUser = User::create([
            'name' => 'Budi Sudrajat',
            'email' => 'budi@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // Gold Member 2 (As documented on login badge)
        $samuelUser = User::create([
            'name' => 'Samuel',
            'email' => 'samuel@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // Silver Member 1
        $sitiUser = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // Silver Member 2
        $dewiUser = User::create([
            'name' => 'Dewi Sartika',
            'email' => 'dewi@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // Bronze Member 1
        $rudiUser = User::create([
            'name' => 'Rudi Hermawan',
            'email' => 'rudi@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // Bronze Member 2
        $ahmadUser = User::create([
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@tkp.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);

        // 2. Seed Pelanggan profiles
        $budi = Pelanggan::create([
            'user_id' => $budiUser->id,
            'kode_pelanggan' => 'PLG-0001',
            'nama' => 'Budi Sudrajat',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Mawar No. 12, Jakarta',
            'poin' => 120,
            'status_member' => 'Gold',
        ]);

        $samuel = Pelanggan::create([
            'user_id' => $samuelUser->id,
            'kode_pelanggan' => 'PLG-0002',
            'nama' => 'Samuel',
            'no_telp' => '081298765432',
            'alamat' => 'Jl. Senopati No. 44, Jakarta',
            'poin' => 95,
            'status_member' => 'Gold',
        ]);

        $siti = Pelanggan::create([
            'user_id' => $sitiUser->id,
            'kode_pelanggan' => 'PLG-0003',
            'nama' => 'Siti Aminah',
            'no_telp' => '081987654321',
            'alamat' => 'Jl. Melati No. 5, Bandung',
            'poin' => 45,
            'status_member' => 'Silver',
        ]);

        $dewi = Pelanggan::create([
            'user_id' => $dewiUser->id,
            'kode_pelanggan' => 'PLG-0004',
            'nama' => 'Dewi Sartika',
            'no_telp' => '087812349999',
            'alamat' => 'Jl. Dago No. 102, Bandung',
            'poin' => 55,
            'status_member' => 'Silver',
        ]);

        $rudi = Pelanggan::create([
            'user_id' => $rudiUser->id,
            'kode_pelanggan' => 'PLG-0005',
            'nama' => 'Rudi Hermawan',
            'no_telp' => '08561234567',
            'alamat' => 'Jl. Kenanga No. 88, Bogor',
            'poin' => 15,
            'status_member' => 'Bronze',
        ]);

        $ahmad = Pelanggan::create([
            'user_id' => $ahmadUser->id,
            'kode_pelanggan' => 'PLG-0006',
            'nama' => 'Ahmad Dani',
            'no_telp' => '081377889900',
            'alamat' => 'Jl. Cibubur Raya No. 17, Depok',
            'poin' => 10,
            'status_member' => 'Bronze',
        ]);

        // 3. Seed LoyalitasPelanggan
        LoyalitasPelanggan::create([
            'pelanggan_id' => $budi->id,
            'total_akumulasi_transaksi' => 1500000.00,
            'level_member' => 'Gold',
            'diskon_khusus' => 10,
            'tanggal_bergabung' => Carbon::now()->subMonths(6),
        ]);

        LoyalitasPelanggan::create([
            'pelanggan_id' => $samuel->id,
            'total_akumulasi_transaksi' => 1250000.00,
            'level_member' => 'Gold',
            'diskon_khusus' => 10,
            'tanggal_bergabung' => Carbon::now()->subMonths(5),
        ]);

        LoyalitasPelanggan::create([
            'pelanggan_id' => $siti->id,
            'total_akumulasi_transaksi' => 650000.00,
            'level_member' => 'Silver',
            'diskon_khusus' => 5,
            'tanggal_bergabung' => Carbon::now()->subMonths(3),
        ]);

        LoyalitasPelanggan::create([
            'pelanggan_id' => $dewi->id,
            'total_akumulasi_transaksi' => 580000.00,
            'level_member' => 'Silver',
            'diskon_khusus' => 5,
            'tanggal_bergabung' => Carbon::now()->subMonths(2),
        ]);

        LoyalitasPelanggan::create([
            'pelanggan_id' => $rudi->id,
            'total_akumulasi_transaksi' => 180000.00,
            'level_member' => 'Bronze',
            'diskon_khusus' => 0,
            'tanggal_bergabung' => Carbon::now()->subMonth(),
        ]);

        LoyalitasPelanggan::create([
            'pelanggan_id' => $ahmad->id,
            'total_akumulasi_transaksi' => 120000.00,
            'level_member' => 'Bronze',
            'diskon_khusus' => 0,
            'tanggal_bergabung' => Carbon::now()->subWeeks(2),
        ]);

        // 4. Seed Produk
        $produkData = [
            [
                'nama_produk' => 'Espresso Double',
                'kategori' => 'Kopi',
                'harga' => 15000.00,
                'deskripsi' => 'Ekstrak kopi murni yang pekat dan kuat dari biji kopi pilihan.',
                'stok' => 99,
                'gambar' => 'uploads/produk/1794565623_espresso_double.jpg',
            ],
            [
                'nama_produk' => 'Kopi Susu TKP (Signature)',
                'kategori' => 'Kopi',
                'harga' => 22000.00,
                'deskripsi' => 'Espresso dengan susu segar hangat, krimer nabati, dan sirup gula aren khas TKP.',
                'stok' => 150,
                'gambar' => 'uploads/produk/1794565765_kopi_susu_tkp_(signature).jpg',
            ],
            [
                'nama_produk' => 'Cafe Latte',
                'kategori' => 'Kopi',
                'harga' => 20000.00,
                'deskripsi' => 'Satu shot espresso dengan susu steam dan foam tipis di atasnya.',
                'stok' => 80,
                'gambar' => 'uploads/produk/1794564783_cafe_latte.jpg',
            ],
            [
                'nama_produk' => 'Cappuccino',
                'kategori' => 'Kopi',
                'harga' => 20000.00,
                'deskripsi' => 'Espresso dengan porsi susu steam dan foam tebal seimbang.',
                'stok' => 75,
                'gambar' => 'uploads/produk/1794565178_cappuccino.jpg',
            ],
            [
                'nama_produk' => 'Americano',
                'kategori' => 'Kopi',
                'harga' => 18000.00,
                'deskripsi' => 'Espresso shot yang diencerkan dengan air panas.',
                'stok' => 120,
                'gambar' => 'uploads/produk/1794564621_americano.jpg',
            ],
            [
                'nama_produk' => 'Matcha Latte Premium',
                'kategori' => 'Non-Kopi',
                'harga' => 24000.00,
                'deskripsi' => 'Matcha bubuk asli Jepang berpadu dengan susu segar lembut.',
                'stok' => 60,
                'gambar' => 'uploads/produk/1794565828_matcha_latte_premium.jpg',
            ],
            [
                'nama_produk' => 'Chocolate Signature',
                'kategori' => 'Non-Kopi',
                'harga' => 23000.00,
                'deskripsi' => 'Cokelat hitam premium pekat dengan susu segar manis.',
                'stok' => 70,
                'gambar' => 'uploads/produk/1794565314_chocolate_signature.webp',
            ],
            [
                'nama_produk' => 'Croissant Butter',
                'kategori' => 'Makanan',
                'harga' => 18000.00,
                'deskripsi' => 'Pastry khas Perancis bertekstur renyah di luar dan lembut di dalam dengan mentega berkualitas.',
                'stok' => 30,
                'gambar' => 'uploads/produk/1794565489_croissant_butter.webp',
            ],
            [
                'nama_produk' => 'French Fries Spicy',
                'kategori' => 'Cemilan',
                'harga' => 16000.00,
                'deskripsi' => 'Kentang goreng renyah bumbu pedas manis gurih.',
                'stok' => 45,
                'gambar' => 'uploads/produk/1794565695_french_fries_spicy.jpg',
            ],
            [
                'nama_produk' => 'Red Velvet Slice',
                'kategori' => 'Cemilan',
                'harga' => 25000.00,
                'deskripsi' => 'Kue red velvet lembut dengan cream cheese lezat di tiap lapisannya.',
                'stok' => 20,
                'gambar' => 'uploads/produk/1794565896_red_velvet_slice.jpg',
            ],
        ];

        $produkModels = [];
        foreach ($produkData as $data) {
            $produkModels[] = Produk::create($data);
        }

        // 5. Seed Promosi
        Promosi::create([
            'nama_promo' => 'Diskon Awal Tahun TKP',
            'kode_promo' => 'TKPAWAL10',
            'deskripsi' => 'Potongan 10% untuk semua pembelian menu kopi, khusus pelanggan terdaftar.',
            'diskon_persen' => 10,
            'diskon_nominal' => 0.00,
            'minimal_transaksi' => 30000.00,
            'poin_dibutuhkan' => 0,
            'tanggal_mulai' => Carbon::now()->subMonths(1),
            'tanggal_selesai' => Carbon::now()->addMonths(2),
            'status' => true,
        ]);

        Promosi::create([
            'nama_promo' => 'Klaim Kopi Susu Free',
            'kode_promo' => 'TKPFREEPOIN',
            'deskripsi' => 'Tukarkan 50 poin loyalitas kamu dengan 1 cup Kopi Susu TKP gratis!',
            'diskon_persen' => 0,
            'diskon_nominal' => 22000.00,
            'minimal_transaksi' => 22000.00,
            'poin_dibutuhkan' => 50,
            'tanggal_mulai' => Carbon::now()->subMonths(1),
            'tanggal_selesai' => Carbon::now()->addMonths(6),
            'status' => true,
        ]);

        Promosi::create([
            'nama_promo' => 'Diskon Khusus Gajian',
            'kode_promo' => 'TKPGAJIAN',
            'deskripsi' => 'Potongan langsung Rp 15.000 dengan minimal belanja Rp 75.000.',
            'diskon_persen' => 0,
            'diskon_nominal' => 15000.00,
            'minimal_transaksi' => 75000.00,
            'poin_dibutuhkan' => 0,
            'tanggal_mulai' => Carbon::now()->subDays(5),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'status' => true,
        ]);

        // 6. Seed Transaksi and DetailTransaksi up to TODAY
        $allMembers = [$budi, $samuel, $siti, $dewi, $rudi, $ahmad];
        $paymentMethods = ['Cash', 'QRIS'];

        $trxCounter = 1;

        // Generate transactions for the last 30 days including today
        for ($i = 29; $i >= 0; $i--) {
            $baseDay = Carbon::now()->subDays($i);
            
            // 2 to 5 transactions per day
            $transCount = rand(2, 5);
            for ($t = 0; $t < $transCount; $t++) {
                $trxTime = $baseDay->copy()->setHour(rand(8, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));
                
                // 70% chance of registered member, 30% guest walk-in
                $pel = (rand(1, 10) <= 7) ? $allMembers[array_rand($allMembers)] : null;
                $payment = $paymentMethods[array_rand($paymentMethods)];
                
                // Select 1 to 3 random products
                $itemsCount = rand(1, 3);
                $selectedProducts = array_rand($produkModels, $itemsCount);
                if (!is_array($selectedProducts)) {
                    $selectedProducts = [$selectedProducts];
                }

                $totalHarga = 0;
                $details = [];

                foreach ($selectedProducts as $idx) {
                    $prod = $produkModels[$idx];
                    $qty = rand(1, 2);
                    $subtotal = $prod->harga * $qty;
                    $totalHarga += $subtotal;

                    $details[] = [
                        'produk_id' => $prod->id,
                        'jumlah' => $qty,
                        'harga_satuan' => $prod->harga,
                        'subtotal' => $subtotal,
                    ];
                }

                // Check discount
                $diskon = 0;
                $poinDigunakan = 0;
                if ($pel) {
                    $level = $pel->status_member;
                    if ($level === 'Gold') {
                        $diskon = $totalHarga * 0.10; // 10%
                    } elseif ($level === 'Silver') {
                        $diskon = $totalHarga * 0.05; // 5%
                    }

                    if (rand(1, 10) > 8 && $pel->poin >= 50) {
                        $poinDigunakan = 50;
                        $diskon += 22000.00;
                    }
                }

                $totalAkhir = max(0, $totalHarga - $diskon);
                $totalBayar = 0;
                $kembalian = 0;

                if ($payment === 'Cash') {
                    if ($totalAkhir <= 20000) {
                        $totalBayar = 20000;
                    } elseif ($totalAkhir <= 50000) {
                        $totalBayar = 50000;
                    } elseif ($totalAkhir <= 100000) {
                        $totalBayar = 100000;
                    } else {
                        $totalBayar = ceil($totalAkhir / 50000) * 50000;
                    }
                    $kembalian = $totalBayar - $totalAkhir;
                } else {
                    $totalBayar = $totalAkhir;
                    $kembalian = 0;
                }

                $poinDapat = floor($totalAkhir / 10000);
                $trxCode = 'TRX-' . $trxTime->format('Ymd') . '-' . str_pad($trxCounter++, 4, '0', STR_PAD_LEFT);

                $transaksi = Transaksi::create([
                    'kode_transaksi' => $trxCode,
                    'pelanggan_id' => $pel ? $pel->id : null,
                    'user_id' => $baristaUser->id,
                    'total_harga' => $totalHarga,
                    'diskon' => $diskon,
                    'total_akhir' => $totalAkhir,
                    'total_bayar' => $totalBayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => $payment,
                    'status_pembayaran' => 'Lunas',
                    'poin_dapat' => $poinDapat,
                    'poin_digunakan' => $poinDigunakan,
                    'created_at' => $trxTime,
                    'updated_at' => $trxTime,
                ]);

                foreach ($details as $det) {
                    $det['transaksi_id'] = $transaksi->id;
                    $det['created_at'] = $trxTime;
                    $det['updated_at'] = $trxTime;
                    DetailTransaksi::create($det);
                }
            }
        }
    }
}
