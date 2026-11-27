<?php

namespace Tests\Feature;

use App\Models\BelanjaBahan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\TutupBuku;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_only_three_roles_exist_and_barista_is_rejected(): void
    {
        $this->assertSame(['kasir', 'owner', 'pelanggan'], User::query()->distinct()->orderBy('role')->pluck('role')->all());

        $owner = User::where('role', 'owner')->firstOrFail();
        $this->actingAs($owner)->post('/owner/pengguna', [
            'name' => 'Coba Barista',
            'email' => 'barista-baru@tkp.com',
            'role' => 'barista',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('role');
    }

    public function test_kasir_accounts_are_never_members(): void
    {
        $kasirIds = User::where('role', 'kasir')->pluck('id');
        $this->assertSame(2, $kasirIds->count());
        $this->assertSame(0, Pelanggan::whereIn('user_id', $kasirIds)->count());
        $this->assertSame(6, Pelanggan::member()->count());

        $owner = User::where('role', 'owner')->firstOrFail();
        $this->actingAs($owner)->get('/owner/pelanggan')->assertOk()
            ->assertDontSee('Owit')
            ->assertDontSee('Tantri');
        $this->actingAs($owner)->get('/kasir/transaksi/baru')->assertOk()
            ->assertDontSee('Owit (')
            ->assertDontSee('Tantri (');
    }

    public function test_kasir_area_is_only_for_owner_and_kasir(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $member = User::where('no_telp', '081234567890')->firstOrFail();

        $this->actingAs($kasir)->get('/kasir/dashboard')->assertOk();
        $this->actingAs($member)->get('/kasir/dashboard')->assertForbidden();
    }

    public function test_loyalitas_menu_is_removed_from_owner_area(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->get('/owner/loyalitas')->assertNotFound();
        $this->actingAs($owner)->get('/owner/dashboard')->assertOk()->assertDontSee('Loyalitas Pelanggan');
    }

    public function test_member_registers_with_name_and_phone_and_logs_in_with_phone(): void
    {
        // Sebagai tamu: validasi nomor telepon memakai pesan Bahasa Indonesia (bukan kunci terjemahan)
        $this->post('/register', [
            'name' => 'Tanpa Telepon',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors(['no_telp']);
        $this->assertStringNotContainsString('validation.', (string) session('errors')->first('no_telp'));

        $this->post('/register', [
            'name' => 'Rian Pratama',
            'no_telp' => '+62 812-3456-7999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('no_telp', '081234567999')->firstOrFail();
        $this->assertNull($user->email);
        $this->assertDatabaseHas('pelanggan', [
            'user_id' => $user->id,
            'nama' => 'Rian Pratama',
            'no_telp' => '081234567999',
            'status_member' => 'Bronze',
        ]);

        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', [
            'email' => '+62 812-3456-7999',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_links_existing_offline_member_with_same_phone(): void
    {
        $offline = Pelanggan::create([
            'user_id' => null,
            'kode_pelanggan' => Pelanggan::kodeBerikutnya(),
            'nama' => 'Dewi Offline',
            'no_telp' => '08999000111',
            'poin' => 30,
            'status_member' => 'Silver',
            'total_belanja' => 500000,
            'tanggal_bergabung' => now()->subMonth()->toDateString(),
        ]);

        $this->post('/register', [
            'name' => 'Dewi Online',
            'no_telp' => '08999000111',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $offline->refresh();
        $this->assertNotNull($offline->user_id);
        $this->assertSame(30, $offline->poin);
        $this->assertSame(1, Pelanggan::where('no_telp', '08999000111')->count());
    }

    public function test_staff_can_still_login_with_email(): void
    {
        $this->post('/login', [
            'email' => 'owner@tkp.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs(User::where('email', 'owner@tkp.com')->firstOrFail());
    }

    public function test_owner_registers_member_with_name_and_phone_only(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->post('/owner/pelanggan', [
            'nama' => 'Sari Offline',
            'no_telp' => '08133334444',
            'poin' => 0,
            'status_member' => 'Bronze',
        ])->assertRedirect(route('owner.pelanggan.index'));

        $this->assertDatabaseHas('pelanggan', ['nama' => 'Sari Offline', 'user_id' => null, 'no_telp' => '08133334444']);

        $this->actingAs($owner)->post('/owner/pelanggan', [
            'nama' => 'Duplikat',
            'no_telp' => '08133334444',
            'poin' => 0,
            'status_member' => 'Bronze',
        ])->assertSessionHasErrors('no_telp');
    }

    public function test_pengguna_with_pelanggan_role_needs_phone_and_gets_member_profile(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->post('/owner/pengguna', [
            'name' => 'Tanpa Telp',
            'role' => 'pelanggan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('no_telp');

        $this->actingAs($owner)->post('/owner/pengguna', [
            'name' => 'Member Baru',
            'role' => 'pelanggan',
            'no_telp' => '0812 9999 0000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $user = User::where('no_telp', '081299990000')->firstOrFail();
        $this->assertNull($user->email);
        $this->assertDatabaseHas('pelanggan', ['user_id' => $user->id, 'no_telp' => '081299990000']);
    }

    public function test_online_and_offline_checkout_earn_points_at_their_own_rate(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $produk = $this->produkUji(20000);
        $member = Pelanggan::where('status_member', 'Bronze')->firstOrFail();
        $poinAwal = $member->poin;

        $this->actingAs($kasir)->postJson('/kasir/transaksi/checkout', $this->payloadCheckout($produk, $member, 'Offline'))
            ->assertOk()->assertJson(['success' => true]);
        $offline = Transaksi::latest('id')->firstOrFail();
        $this->assertSame('Offline', $offline->sumber_transaksi);
        $this->assertSame(4, $offline->poin_dapat); // Rp 40.000 / Rp 10.000

        $this->actingAs($kasir)->postJson('/kasir/transaksi/checkout', $this->payloadCheckout($produk, $member, 'Online'))
            ->assertOk();
        $online = Transaksi::latest('id')->firstOrFail();
        $this->assertSame('Online', $online->sumber_transaksi);
        $this->assertSame(2, $online->poin_dapat); // Rp 40.000 / Rp 20.000

        $this->assertSame($poinAwal + 4 + 2, $member->fresh()->poin);
    }

    public function test_member_level_follows_total_belanja(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $produk = $this->produkUji(20000);
        $member = Pelanggan::where('status_member', 'Silver')->firstOrFail();
        $member->update(['total_belanja' => 990000]);

        // Silver mendapat diskon 5%: Rp 19.000 dibayar, total belanja jadi Rp 1.009.000 -> Gold
        $this->actingAs($kasir)->postJson('/kasir/transaksi/checkout', $this->payloadCheckout($produk, $member, 'Offline', 1, 20000))
            ->assertOk();

        $member->refresh();
        $this->assertSame('Gold', $member->status_member);
        $this->assertEquals(1009000, (float) $member->total_belanja);
    }

    public function test_pelanggan_card_shows_points_and_owner_can_print_it(): void
    {
        $user = User::where('no_telp', '081234567890')->firstOrFail(); // Budi, Gold, 120 poin
        $this->actingAs($user)->get('/pelanggan/kartu')->assertOk()
            ->assertSee('Poin Reward')
            ->assertSee('Cetak Kartu')
            ->assertSee('120');

        $owner = User::where('role', 'owner')->firstOrFail();
        $member = Pelanggan::where('no_telp', '081234567890')->firstOrFail();
        $this->actingAs($owner)->get('/owner/pelanggan/'.$member->id.'/kartu')->assertOk()
            ->assertSee($member->kode_pelanggan);
    }

    public function test_member_can_send_kritik_saran_and_owner_can_read_it(): void
    {
        $member = User::where('no_telp', '081234567890')->firstOrFail();

        $this->actingAs($member)->post('/pelanggan/kritik-saran', [
            'jenis' => 'Kritik',
            'isi' => 'Kopi susunya kurang manis menurut saya.',
        ])->assertRedirect();
        $this->assertDatabaseHas('kritik_saran', ['jenis' => 'Kritik']);

        $owner = User::where('role', 'owner')->firstOrFail();
        $this->actingAs($owner)->get('/owner/kritik-saran')->assertOk()->assertSee('kurang manis');
        $this->actingAs($member)->get('/pelanggan/kritik-saran')->assertOk()->assertSee('kurang manis');
    }

    public function test_menu_cards_open_a_detail_modal(): void
    {
        $member = User::where('no_telp', '081234567890')->firstOrFail();

        $this->actingAs($member)->get('/pelanggan/produk')->assertOk()
            ->assertSee('id="menuDetailModal"', false)
            ->assertSee('btn-menu-detail', false);
    }

    public function test_hari_penting_lists_upcoming_birthdays_of_loyal_members_only(): void
    {
        $budi = Pelanggan::where('no_telp', '081234567890')->firstOrFail(); // Gold, ulang tahun 5 hari lagi
        $hariPenting = Pelanggan::hariPenting(Carbon::now(), 30);

        $entriBudi = $hariPenting->first(fn ($item) => $item['pelanggan']->id === $budi->id && $item['jenis'] === 'Ulang tahun');
        $this->assertNotNull($entriBudi);
        $this->assertSame(5, $entriBudi['sisa_hari']);

        $bronzeIds = Pelanggan::where('status_member', 'Bronze')->pluck('id');
        $this->assertFalse($hariPenting->contains(fn ($item) => $bronzeIds->contains($item['pelanggan']->id)));

        $owner = User::where('role', 'owner')->firstOrFail();
        $this->actingAs($owner)->get('/owner/dashboard')->assertOk()->assertSee('Hari Penting Pelanggan Setia');
    }

    public function test_laporan_filters_by_day_week_and_month(): void
    {
        Transaksi::query()->delete();
        Carbon::setTestNow(Carbon::create(2026, 3, 4, 12));

        $kasir = User::where('role', 'kasir')->firstOrFail();
        $owner = User::where('role', 'owner')->firstOrFail();
        $senin = Carbon::create(2026, 3, 2, 10);
        $rabu = Carbon::create(2026, 3, 4, 10);
        $bulanLalu = Carbon::create(2026, 2, 15, 9);

        foreach ([['TRX-T-1', $senin, 10000], ['TRX-T-2', $rabu, 25000], ['TRX-T-3', $bulanLalu, 99000]] as [$kode, $waktu, $total]) {
            Transaksi::create([
                'kode_transaksi' => $kode,
                'user_id' => $kasir->id,
                'total_harga' => $total,
                'diskon' => 0,
                'total_akhir' => $total,
                'total_bayar' => $total,
                'kembalian' => 0,
                'metode_pembayaran' => 'Cash',
                'sumber_transaksi' => 'Offline',
                'status_pembayaran' => 'Lunas',
                'poin_dapat' => 0,
                'poin_digunakan' => 0,
            ])->forceFill(['created_at' => $waktu, 'updated_at' => $waktu])->save();
        }

        $harian = $this->actingAs($owner)->get('/owner/laporan?periode=harian&tanggal=2026-03-02');
        $harian->assertOk();
        $this->assertSame(1, $harian->viewData('ringkasan')['transaksi']);
        $this->assertEquals(10000, $harian->viewData('ringkasan')['omset']);

        $mingguan = $this->actingAs($owner)->get('/owner/laporan?periode=mingguan&tanggal=2026-03-04');
        $this->assertEquals(35000, $mingguan->viewData('ringkasan')['omset']);
        $this->assertCount(7, $mingguan->viewData('rincian'));

        $bulanan = $this->actingAs($owner)->get('/owner/laporan?periode=bulanan&tanggal=2026-02-15');
        $this->assertEquals(99000, $bulanan->viewData('ringkasan')['omset']);

        $this->actingAs($owner)->get('/owner/transaksi?periode=mingguan&tanggal=2026-03-04')->assertOk()
            ->assertViewHas('transaksi', fn ($trx) => $trx->count() === 2);
    }

    public function test_belanja_bahan_is_summed_per_week_and_month(): void
    {
        BelanjaBahan::query()->delete();
        Carbon::setTestNow(Carbon::create(2026, 3, 4, 12));
        $owner = User::where('role', 'owner')->firstOrFail();

        BelanjaBahan::create(['tanggal' => '2026-03-02', 'nama_bahan' => 'Susu', 'kategori' => 'Susu & Krimer', 'jumlah' => 2, 'satuan' => 'liter', 'harga_total' => 50000, 'user_id' => $owner->id]);
        BelanjaBahan::create(['tanggal' => '2026-03-04', 'nama_bahan' => 'Biji Kopi', 'kategori' => 'Biji Kopi', 'jumlah' => 1, 'satuan' => 'kg', 'harga_total' => 120000, 'user_id' => $owner->id]);
        BelanjaBahan::create(['tanggal' => '2026-02-20', 'nama_bahan' => 'Gula Aren', 'kategori' => 'Sirup & Gula', 'jumlah' => 1, 'satuan' => 'kg', 'harga_total' => 30000, 'user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/owner/belanja-bahan?periode=mingguan&tanggal=2026-03-04');
        $response->assertOk();
        $this->assertEquals(170000, $response->viewData('totalPeriode'));
        $this->assertEquals(170000, $response->viewData('rekapMinggu')->first()['modal']); // minggu ini
        $this->assertEquals(170000, $response->viewData('rekapBulan')->first()['modal']);  // Maret
        $this->assertEquals(30000, $response->viewData('rekapBulan')->get(1)['modal']);    // Februari
    }

    public function test_tutup_buku_locks_belanja_in_closed_period_until_reopened(): void
    {
        BelanjaBahan::query()->delete();
        Carbon::setTestNow(Carbon::create(2026, 3, 4, 12));
        $owner = User::where('role', 'owner')->firstOrFail();

        BelanjaBahan::create(['tanggal' => '2026-02-10', 'nama_bahan' => 'Susu', 'kategori' => 'Susu & Krimer', 'jumlah' => 3, 'satuan' => 'liter', 'harga_total' => 75000, 'user_id' => $owner->id]);

        // Periode yang belum berakhir (Maret) tidak bisa ditutup
        $this->actingAs($owner)->post('/owner/tutup-buku', ['tipe' => 'bulanan', 'tanggal' => '2026-03-01'])
            ->assertSessionHas('error');
        $this->assertSame(0, TutupBuku::count());

        // Februari sudah berakhir: tutup buku
        $this->actingAs($owner)->post('/owner/tutup-buku', ['tipe' => 'bulanan', 'tanggal' => '2026-02-01'])
            ->assertSessionHas('success');
        $buku = TutupBuku::firstOrFail();
        $this->assertSame('Bulanan', $buku->tipe);
        $this->assertEquals(75000, (float) $buku->total_modal);

        // Belanja yang tanggalnya masuk periode tertutup ditolak
        $belanjaTelat = [
            'tanggal' => '2026-02-20',
            'nama_bahan' => 'Telur',
            'kategori' => 'Bahan Makanan',
            'jumlah' => 10,
            'satuan' => 'pcs',
            'harga_total' => 40000,
        ];
        $this->actingAs($owner)->post('/owner/belanja-bahan', $belanjaTelat)->assertSessionHas('error');
        $this->assertSame(1, BelanjaBahan::count());

        // Setelah dibuka kembali, belanja diterima
        $this->actingAs($owner)->post('/owner/tutup-buku/'.$buku->id.'/buka')->assertSessionHas('success');
        $this->actingAs($owner)->post('/owner/belanja-bahan', $belanjaTelat)->assertRedirect();
        $this->assertSame(2, BelanjaBahan::count());
    }

    public function test_pelanggan_menu_and_card_routes_are_available_to_members_only(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();
        $this->actingAs($owner)->get('/pelanggan/kartu')->assertForbidden();
    }

    public function test_all_new_and_changed_pages_render_for_their_roles(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $member = User::where('no_telp', '081234567890')->firstOrFail();
        $budi = Pelanggan::where('no_telp', '081234567890')->firstOrFail();

        $this->actingAs($owner)->get('/owner/tutup-buku')->assertOk()->assertSee('Periode yang Bisa Ditutup');
        $this->actingAs($owner)->get('/owner/belanja-bahan/create')->assertOk()->assertSee('Catat Belanja Bahan');
        $this->actingAs($owner)->get('/owner/belanja-bahan/'.BelanjaBahan::firstOrFail()->id.'/edit')->assertOk();
        $this->actingAs($owner)->get('/owner/kritik-saran')->assertOk()->assertSee('Kritik');
        $this->actingAs($owner)->get('/owner/pelanggan/create')->assertOk()->assertDontSee('Alamat Email');
        $this->actingAs($owner)->get('/owner/pelanggan/'.$budi->id.'/edit')->assertOk()->assertSee('Akun Login');
        $this->actingAs($owner)->get('/owner/pelanggan')->assertOk()->assertSee('Kartu Member');
        $this->actingAs($owner)->get('/owner/pengguna/create')->assertOk()->assertSee('No. Telepon (wajib untuk Pelanggan)');
        $this->actingAs($owner)->get('/owner/pengguna/'.$kasir->id.'/edit')->assertOk();
        $this->actingAs($owner)->get('/owner/pengguna')->assertOk()->assertSee('Kasir');
        $this->actingAs($owner)->get('/owner/laporan?periode=bulanan')->assertOk()->assertSee('Modal Belanja Bahan');
        $this->actingAs($owner)->get('/owner/laporan/export-pdf?periode=bulanan')->assertOk()->assertSee('Selisih omset');
        $this->actingAs($owner)->get('/owner/laporan/export-excel?periode=mingguan')->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->actingAs($owner)->get('/owner/produk')->assertOk();

        $this->actingAs($kasir)->get('/kasir/dashboard')->assertOk()->assertSee('Dashboard Kasir');
        $this->actingAs($kasir)->get('/kasir/transaksi')->assertOk();
        $this->actingAs($kasir)->get('/kasir/produk')->assertOk();
        $this->actingAs($kasir)->get('/kasir/transaksi/'.Transaksi::firstOrFail()->id)->assertOk()->assertSee('KASIR:');

        $this->actingAs($member)->get('/pelanggan/dashboard')->assertOk()->assertSee('Kartu Member');
        $this->actingAs($member)->get('/pelanggan/transaksi')->assertOk()->assertSee('Kasir');
        $this->actingAs($member)->get('/pelanggan/promosi')->assertOk()->assertSee('Poin Reward Anda');
        $this->actingAs($member)->get('/profile')->assertOk()->assertSee('No. Telepon (untuk login member)');
    }

    private function produkUji(int $harga): Produk
    {
        return Produk::create([
            'nama_produk' => 'Uji Poin '.$harga,
            'kategori' => 'Kopi',
            'harga' => $harga,
            'stok' => 50,
            'deskripsi' => 'Produk uji.',
            'gambar' => null,
        ]);
    }

    private function payloadCheckout(Produk $produk, ?Pelanggan $member, string $sumber, int $qty = 2, int $totalBayar = 40000): array
    {
        return [
            'cart' => [['produk_id' => $produk->id, 'qty' => $qty]],
            'pelanggan_id' => $member?->id,
            'kode_promo' => null,
            'poin_digunakan' => 0,
            'metode_pembayaran' => 'Cash',
            'sumber_transaksi' => $sumber,
            'total_bayar' => $totalBayar,
        ];
    }
}
