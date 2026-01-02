<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_persediaan', function (Blueprint $table) {
            $table->enum('jenis_barang', ['barang_dagang', 'bahan_baku', 'barang_jadi', 'barang_dalam_proses', 'aset_biologis', 'jasa'])
                  ->default('barang_dagang')
                  ->after('nama_barang');
            
            $table->unsignedBigInteger('id_cabang')->nullable()->after('id_barang');
            $table->decimal('stok_minimum', 10, 2)->default(0)->after('stok_saat_ini');
        });

        // Add id_cabang to transaction tables
        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang')->nullable()->after('id_barang');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang')->nullable()->after('id_penjualan');
        });

        Schema::table('pembelian', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang')->nullable()->after('id_pembelian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn('id_cabang');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('id_cabang');
        });

        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->dropColumn('id_cabang');
        });

        Schema::table('master_persediaan', function (Blueprint $table) {
            $table->dropColumn('stok_minimum');
            $table->dropColumn('id_cabang');
            $table->dropColumn('jenis_barang');
        });
    }
};
