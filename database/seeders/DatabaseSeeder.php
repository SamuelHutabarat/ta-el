<?php

namespace Database\Seeders;

use App\Models\BelanjaBahan;
use App\Models\DetailTransaksi;
use App\Models\KritikSaran;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Akun staf: 1 owner dan 2 kasir (tidak pernah menjadi member)
        $owner = User::create([
            'name' => 'Owner TKP',
            'email' => 'owner@tkp.com',
            'password' => $password,
            'role' => 'owner',
        ]);

        $kasirs = [
            User::create(['name' => 'Owit', 'email' => 'owit@tkp.com', 'password' => $password, 'role' => 'kasir']),
            User::create(['name' => 'Tantri', 'email' => 'tantri@tkp.com', 'password' => $password, 'role' => 'kasir']),
        ];

        // 2. Member: login memakai nomor telepon (tanpa email)
        $daftarMember = [
            ['nama' => 'Budi Sudrajat', 'no_telp' => '081234567890', 'status' => 'Gold', 'poin' => 120, 'total' => 1500000, 'gabung' => Carbon::now()->subMonths(6), 'lahir' => Carbon::now()->addDays(5)->subYears(28)],
            ['nama' => 'Samuel', 'no_telp' => '081298765432', 'status' => 'Gold', 'poin' => 95, 'total' => 1250000, 'gabung' => Carbon::now()->subMonths(5), 'lahir' => Carbon::now()->addDays(60)->subYears(30)],
            ['nama' => 'Siti Aminah', 'no_telp' => '081987654321', 'status' => 'Silver', 'poin' => 45, 'total' => 650000, 'gabung' => Carbon::now()->subMonths(3), 'lahir' => Carbon::now()->addDays(20)->subYears(31)],
            ['nama' => 'Dewi Sartika', 'no_telp' => '087812349999', 'status' => 'Silver', 'poin' => 55, 'total' => 580000, 'gabung' => Carbon::now()->subMonths(2), 'lahir' => Carbon::now()->addDays(10)->subYears(26)],
            ['nama' => 'Rudi Hermawan', 'no_telp' => '08561234567', 'status' => 'Bronze', 'poin' => 15, 'total' => 180000, 'gabung' => Carbon::now()->subMonth(), 'lahir' => null],
            ['nama' => 'Ahmad Dani', 'no_telp' => '081377889900', 'status' => 'Bronze', 'poin' => 10, 'total' => 120000, 'gabung' => Carbon::now()->subWeeks(2), 'lahir' => null],
        ];

        $allMembers = [];
        foreach ($daftarMember as $i => $m) {
            $user = User::create([
                'name' => $m['nama'],
                'email' => null,
                'no_telp' => $m['no_telp'],
                'password' => $password,
                'role' => 'pelanggan',
            ]);

            $allMembers[] = Pelanggan::create([
                'user_id' => $user->id,
                'kode_pelanggan' => 'PLG-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'nama' => $m['nama'],
                'no_telp' => $m['no_telp'],
                'poin' => $m['poin'],
                'status_member' => $m['status'],
                'total_belanja' => $m['total'],
                'tanggal_lahir' => $m['lahir']?->toDateString(),
                'tanggal_bergabung' => $m['gabung']->toDateString(),
            ]);
        }

        // 3. Seed Produk
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


        // 4. Seed Promosi
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
            'deskripsi' => 'Tukarkan 50 poin reward kamu dengan 1 cup Kopi Susu TKP gratis!',
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


        // 5. Seed Transaksi and DetailTransaksi up to TODAY (kanal Offline = datang langsung, Online = pesanan online)
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
                $sumber = rand(1, 4) === 1 ? 'Online' : 'Offline';
                $kasir = $kasirs[$trxCounter % 2];

                // Select 1 to 3 random products
                $itemsCount = rand(1, 3);
                $selectedProducts = array_rand($produkModels, $itemsCount);
                if (! is_array($selectedProducts)) {
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

                // Member discount by tier + optional points redemption
                $diskon = 0;
                $poinDigunakan = 0;
                if ($pel) {
                    $diskon = $totalHarga * ($pel->diskonPersen() / 100);

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

                $poinDapat = floor($totalAkhir / config('crm.rupiah_per_poin.'.$sumber));
                $trxCode = 'TRX-'.$trxTime->format('Ymd').'-'.str_pad((string) $trxCounter++, 4, '0', STR_PAD_LEFT);

                $transaksi = Transaksi::create([
                    'kode_transaksi' => $trxCode,
                    'pelanggan_id' => $pel ? $pel->id : null,
                    'user_id' => $kasir->id,
                    'total_harga' => $totalHarga,
                    'diskon' => $diskon,
                    'total_akhir' => $totalAkhir,
                    'total_bayar' => $totalBayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => $payment,
                    'sumber_transaksi' => $sumber,
                    'status_pembayaran' => 'Lunas',
                    'poin_dapat' => $poinDapat,
                    'poin_digunakan' => $poinDigunakan,
                ]);

                // created_at tidak ikut create() (bukan fillable), jadi diisi setelah disimpan.
                $transaksi->forceFill(['created_at' => $trxTime, 'updated_at' => $trxTime])->save();

                foreach ($details as $det) {
                    $det['transaksi_id'] = $transaksi->id;
                    $det['created_at'] = $trxTime;
                    $det['updated_at'] = $trxTime;
                    DetailTransaksi::create($det);
                }
            }
        }

        // 6. Seed modal belanja bahan (8 minggu terakhir)
        $belanjaRutin = [
            ['Biji Kopi Arabika Blend', 'Biji Kopi', 2, 'kg', 240000, 7],
            ['Susu Full Cream', 'Susu & Krimer', 12, 'liter', 204000, 7],
            ['Krimer Nabati', 'Susu & Krimer', 4, 'liter', 150000, 14],
            ['Gula Aren Cair', 'Sirup & Gula', 3, 'liter', 96000, 14],
            ['Bubuk Matcha Jepang', 'Bahan Makanan', 0.5, 'kg', 160000, 21],
            ['Cokelat Blok Premium', 'Bahan Makanan', 1, 'kg', 150000, 21],
            ['Mentega & Tepung (Croissant)', 'Bahan Makanan', 2, 'paket', 275000, 14],
            ['Kentang Beku', 'Bahan Makanan', 3, 'kg', 90000, 28],
            ['Cup & Tutup Takeaway', 'Kemasan', 1, 'pak', 85000, 28],
        ];

        foreach ($belanjaRutin as [$nama, $kategori, $jumlah, $satuan, $harga, $intervalHari]) {
            for ($offset = 0; $offset <= 56; $offset += $intervalHari) {
                BelanjaBahan::create([
                    'tanggal' => Carbon::today()->subDays(56 - $offset)->toDateString(),
                    'nama_bahan' => $nama,
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan,
                    'harga_total' => $harga,
                    'user_id' => $owner->id,
                ]);
            }
        }

        // 7. Seed kritik & saran
        KritikSaran::create(['pelanggan_id' => $allMembers[0]->id, 'jenis' => 'Saran', 'isi' => 'Mohon tambahkan pilihan susu oat untuk semua menu kopi.']);
        KritikSaran::create(['pelanggan_id' => $allMembers[2]->id, 'jenis' => 'Kritik', 'isi' => 'Antrian saat jam makan siang cukup panjang, mohon kasir ditambah.']);
        KritikSaran::create(['pelanggan_id' => null, 'jenis' => 'Saran', 'isi' => 'Kursi di area luar sebaiknya diberi peneduh.']);
    }
}
