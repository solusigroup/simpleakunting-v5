<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Unit Usaha
        Schema::create('unit_usaha', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cabang');
            $table->string('kode_unit', 20)->unique();
            $table->string('nama_unit', 100);
            $table->string('jenis_usaha', 50)->nullable(); // dagang, jasa, simpan_pinjam, manufaktur, pertanian
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_cabang')->references('id')->on('cabang')->onDelete('cascade');
        });

        // 2. Tambah id_cabang + id_unit_usaha ke tabel transaksi
        $tables = ['jurnal_umum', 'penjualan', 'pembelian', 'simpanan', 'pinjaman', 'master_persediaan'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'id_cabang')) {
                        $table->unsignedBigInteger('id_cabang')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'id_unit_usaha')) {
                        $table->unsignedBigInteger('id_unit_usaha')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['jurnal_umum', 'penjualan', 'pembelian', 'simpanan', 'pinjaman', 'master_persediaan'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $columns = [];
                    if (Schema::hasColumn($tableName, 'id_unit_usaha')) $columns[] = 'id_unit_usaha';
                    if (Schema::hasColumn($tableName, 'id_cabang')) $columns[] = 'id_cabang';
                    if (!empty($columns)) $table->dropColumn($columns);
                });
            }
        }

        Schema::dropIfExists('unit_usaha');
    }
};
