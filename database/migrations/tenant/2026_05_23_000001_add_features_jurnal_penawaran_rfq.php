<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom foto_bukti di jurnal_umum
        if (Schema::hasTable('jurnal_umum') && !Schema::hasColumn('jurnal_umum', 'foto_bukti')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->string('foto_bukti')->nullable()->after('sumber_jurnal');
            });
        }

        // 2. Kolom relasi di tabel penjualan & pembelian
        if (Schema::hasTable('penjualan') && !Schema::hasColumn('penjualan', 'id_penawaran')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->unsignedBigInteger('id_penawaran')->nullable()->after('id_jurnal');
            });
        }
        if (Schema::hasTable('pembelian') && !Schema::hasColumn('pembelian', 'id_rfq')) {
            Schema::table('pembelian', function (Blueprint $table) {
                $table->unsignedBigInteger('id_rfq')->nullable()->after('id_jurnal');
            });
        }

        // 3. Tabel Penawaran Penjualan
        if (!Schema::hasTable('penjualan_penawaran')) {
            Schema::create('penjualan_penawaran', function (Blueprint $table) {
                $table->id('id_penawaran');
                $table->unsignedBigInteger('id_pelanggan');
                $table->unsignedBigInteger('id_cabang');
                $table->unsignedBigInteger('id_unit_usaha');
                $table->string('no_penawaran')->unique();
                $table->date('tanggal_penawaran');
                $table->decimal('total', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->string('status')->default('Draft'); // Draft, Dikirim, Diterima, Ditolak, Dikonversi
                $table->timestamps();
            });
        }

        // 4. Tabel Detail Penawaran Penjualan
        if (!Schema::hasTable('penjualan_penawaran_detail')) {
            Schema::create('penjualan_penawaran_detail', function (Blueprint $table) {
                $table->id('id_detail');
                $table->unsignedBigInteger('id_penawaran');
                $table->unsignedBigInteger('id_barang');
                $table->decimal('kuantitas', 10, 2);
                $table->decimal('harga', 15, 2);
                $table->decimal('subtotal', 15, 2);
                $table->timestamps();
            });
        }

        // 5. Tabel RFQ Pembelian
        if (!Schema::hasTable('pembelian_rfq')) {
            Schema::create('pembelian_rfq', function (Blueprint $table) {
                $table->id('id_rfq');
                $table->unsignedBigInteger('id_pemasok');
                $table->unsignedBigInteger('id_cabang');
                $table->unsignedBigInteger('id_unit_usaha');
                $table->string('no_rfq')->unique();
                $table->date('tanggal_rfq');
                $table->decimal('total', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->string('status')->default('Draft'); // Draft, Dikirim, Disetujui, Dikonversi
                $table->timestamps();
            });
        }

        // 6. Tabel Detail RFQ Pembelian
        if (!Schema::hasTable('pembelian_rfq_detail')) {
            Schema::create('pembelian_rfq_detail', function (Blueprint $table) {
                $table->id('id_detail');
                $table->unsignedBigInteger('id_rfq');
                $table->unsignedBigInteger('id_barang');
                $table->decimal('kuantitas', 10, 2);
                $table->decimal('harga', 15, 2);
                $table->decimal('subtotal', 15, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_rfq_detail');
        Schema::dropIfExists('pembelian_rfq');
        Schema::dropIfExists('penjualan_penawaran_detail');
        Schema::dropIfExists('penjualan_penawaran');

        if (Schema::hasTable('pembelian') && Schema::hasColumn('pembelian', 'id_rfq')) {
            Schema::table('pembelian', function (Blueprint $table) {
                $table->dropColumn('id_rfq');
            });
        }
        if (Schema::hasTable('penjualan') && Schema::hasColumn('penjualan', 'id_penawaran')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->dropColumn('id_penawaran');
            });
        }
        if (Schema::hasTable('jurnal_umum') && Schema::hasColumn('jurnal_umum', 'foto_bukti')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->dropColumn('foto_bukti');
            });
        }
    }
};
