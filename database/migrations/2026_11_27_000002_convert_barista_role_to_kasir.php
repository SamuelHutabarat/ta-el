<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Role "barista" diganti menjadi "kasir" (owner, kasir, pelanggan).
        DB::table('users')->where('role', 'barista')->update([
            'role' => 'kasir',
            'updated_at' => now(),
        ]);

        // Kasir dan owner tidak boleh tercatat sebagai member.
        $stafId = DB::table('users')->whereIn('role', ['owner', 'kasir'])->pluck('id');
        DB::table('pelanggan')->whereIn('user_id', $stafId)->delete();
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'kasir')->update([
            'role' => 'barista',
            'updated_at' => now(),
        ]);
    }
};
