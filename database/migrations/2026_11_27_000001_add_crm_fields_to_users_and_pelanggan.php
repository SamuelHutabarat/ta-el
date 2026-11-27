<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Akun pelanggan kini cukup nama + nomor telepon, jadi email boleh kosong.
            $table->string('email')->nullable()->change();
            $table->string('no_telp', 20)->nullable()->after('email')->index();
        });

        Schema::table('pelanggan', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('no_telp');
            $table->date('tanggal_bergabung')->nullable()->after('status_member');
            $table->decimal('total_belanja', 15, 2)->default(0)->after('tanggal_bergabung');
        });

        // Pindahkan data tanggal bergabung & total belanja dari loyalitas_pelanggan
        // sebelum tabel itu dihapus (lihat migrasi drop_loyalitas_pelanggan_table).
        if (Schema::hasTable('loyalitas_pelanggan')) {
            DB::table('loyalitas_pelanggan')->orderBy('id')->get()->each(function ($loyalitas) {
                DB::table('pelanggan')->where('id', $loyalitas->pelanggan_id)->update([
                    'tanggal_bergabung' => $loyalitas->tanggal_bergabung,
                    'total_belanja' => $loyalitas->total_akumulasi_transaksi,
                ]);
            });
        }

        // Login pelanggan memakai nomor telepon: salin nomor dari profil ke akun login.
        DB::table('pelanggan')
            ->whereNotNull('user_id')
            ->whereNotNull('no_telp')
            ->where('no_telp', '<>', '-')
            ->orderBy('id')
            ->get()
            ->each(function ($pelanggan) {
                DB::table('users')
                    ->where('id', $pelanggan->user_id)
                    ->whereNull('no_telp')
                    ->update(['no_telp' => $pelanggan->no_telp]);
            });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'tanggal_bergabung', 'total_belanja']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['no_telp']);
            $table->dropColumn('no_telp');
        });
    }
};
