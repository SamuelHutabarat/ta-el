<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belanja_bahan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_bahan');
            $table->string('kategori', 50)->default('Lainnya');
            $table->decimal('jumlah', 12, 2)->default(1);
            $table->string('satuan', 20)->default('pcs');
            $table->decimal('harga_total', 15, 2);
            $table->string('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belanja_bahan');
    }
};
