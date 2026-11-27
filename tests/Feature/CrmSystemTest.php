<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CrmSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_owner_can_access_dashboard_with_chart_data(): void
    {
        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartValues');
        $response->assertViewHas('levelLabels');
        $response->assertViewHas('levelCounts');
        $response->assertSee('Grafik Omset 7 Hari Terakhir');
        $response->assertSee('Distribusi Level Member');
    }

    public function test_kasir_can_access_pos_screen(): void
    {
        $kasir = User::where('role', 'kasir')->first();

        $response = $this->actingAs($kasir)->get('/kasir/transaksi/baru');
        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
        $response->assertSee('Pilih Member CRM');
    }

    public function test_pos_checkout_creates_transaction_and_deducts_stock(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $produk = Produk::where('stok', '>=', 5)->first();
        $initialStock = $produk->stok;

        $payload = [
            'cart' => [
                [
                    'produk_id' => $produk->id,
                    'qty' => 2,
                ]
            ],
            'pelanggan_id' => null,
            'kode_promo' => null,
            'poin_digunakan' => 0,
            'metode_pembayaran' => 'Cash',
            'total_bayar' => $produk->harga * 2,
        ];

        $response = $this->actingAs($kasir)
            ->postJson('/kasir/transaksi/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify stock deducted
        $this->assertEquals($initialStock - 2, $produk->fresh()->stok);
    }

    public function test_member_can_access_customer_dashboard(): void
    {
        $memberUser = User::where('role', 'pelanggan')->first();
        $response = $this->actingAs($memberUser)->get('/pelanggan/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Member CRM');
        $response->assertSee('Saldo Poin Anda');
    }

    public function test_user_can_access_profile_edit_with_crm_layout(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun');
        $response->assertSee('Informasi Akun');
        $response->assertSee('Ubah Kata Sandi');
    }

    public function test_product_with_sales_history_cannot_be_deleted(): void
    {
        $owner = User::where('role', 'owner')->first();
        $produkWithSales = Produk::has('detail_transaksi')->first();

        $response = $this->actingAs($owner)
            ->delete('/owner/produk/' . $produkWithSales->id);

        $response->assertRedirect('/owner/produk');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('produk', ['id' => $produkWithSales->id]);
    }

    public function test_kasir_can_open_product_create_and_edit_screens(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $produk = Produk::firstOrFail();

        $this->actingAs($kasir)
            ->get('/kasir/produk/create')
            ->assertOk()
            ->assertSee('Foto Produk');

        $this->get('/kasir/produk/' . $produk->id . '/edit')
            ->assertOk()
            ->assertSee('Ganti Foto Produk');
    }

    public function test_kasir_can_add_a_menu_with_its_photo(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $imagePath = null;
        $temporaryImage = storage_path('framework/testing/menu-photo-test.jpg');
        File::ensureDirectoryExists(dirname($temporaryImage));
        copy(public_path('uploads/produk/1794564783_cafe_latte.jpg'), $temporaryImage);

        try {
            $response = $this->actingAs($kasir)->post('/kasir/produk', [
                'nama_produk' => 'Menu Foto Kasir Test',
                'kategori' => 'Kopi',
                'harga' => 19000,
                'stok' => 12,
                'deskripsi' => 'Produk untuk uji unggah gambar.',
                'gambar' => new \Illuminate\Http\UploadedFile(
                    $temporaryImage,
                    'menu.jpg',
                    'image/jpeg',
                    null,
                    true
                ),
            ]);

            $response->assertRedirect('/kasir/produk');
            $produk = Produk::where('nama_produk', 'Menu Foto Kasir Test')->firstOrFail();
            $imagePath = public_path($produk->gambar);
            $this->assertFileExists($imagePath);
        } finally {
            if ($imagePath && is_file($imagePath)) {
                unlink($imagePath);
            }
            if (is_file($temporaryImage)) {
                unlink($temporaryImage);
            }
        }
    }

    public function test_menu_photo_mapping_and_kasir_roles_are_corrected_by_data_migrations(): void
    {
        // Kondisi database lama: menu Americano memakai foto salah dan staf masih role pelanggan dengan profil member.
        $produk = Produk::where('nama_produk', 'Americano')->firstOrFail();
        $produk->update(['gambar' => 'uploads/produk/espresso_latte.png']);

        foreach (['owit@tkp.com', 'tantri@tkp.com'] as $email) {
            $staf = User::where('email', $email)->firstOrFail();
            $staf->update(['role' => 'pelanggan']);
            Pelanggan::create([
                'user_id' => $staf->id,
                'kode_pelanggan' => Pelanggan::kodeBerikutnya(),
                'nama' => $staf->name,
                'no_telp' => '08111'.random_int(100000, 999999),
                'poin' => 0,
                'status_member' => 'Bronze',
                'total_belanja' => 0,
            ]);
        }

        // Migrasi lama: menandai staf sebagai barista (sebelum role diganti menjadi kasir).
        $migrasiFoto = require base_path('database/migrations/2026_11_25_000001_map_menu_images_and_cashier_roles.php');
        $migrasiFoto->up();

        $this->assertSame('uploads/produk/1794564621_americano.jpg', $produk->fresh()->gambar);
        $this->assertSame(2, User::whereIn('email', ['owit@tkp.com', 'tantri@tkp.com'])->where('role', 'barista')->count());

        // Migrasi baru: barista menjadi kasir dan profil member staf dihapus.
        $migrasiRole = require base_path('database/migrations/2026_11_27_000002_convert_barista_role_to_kasir.php');
        $migrasiRole->up();

        $stafIds = User::whereIn('email', ['owit@tkp.com', 'tantri@tkp.com'])->pluck('id');
        $this->assertSame(2, User::whereIn('email', ['owit@tkp.com', 'tantri@tkp.com'])->where('role', 'kasir')->count());
        $this->assertSame(0, Pelanggan::whereIn('user_id', $stafIds)->count());
        $this->assertSame(0, User::where('role', 'barista')->count());
    }
}
