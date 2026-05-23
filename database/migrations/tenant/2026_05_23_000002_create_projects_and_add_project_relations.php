<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Proyek/Program
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id('id_project');
                $table->string('kode_project', 30)->unique();
                $table->string('nama_project', 100);
                $table->unsignedBigInteger('id_unit_usaha');
                $table->string('status', 20)->default('Aktif'); // Aktif, Selesai
                $table->text('keterangan')->nullable();
                $table->timestamps();

                // Foreign Key
                $table->foreign('id_unit_usaha')->references('id')->on('unit_usaha')->onDelete('cascade');
            });
        }

        // 2. Tambah id_project ke transaksi utama
        if (Schema::hasTable('jurnal_umum') && !Schema::hasColumn('jurnal_umum', 'id_project')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->unsignedBigInteger('id_project')->nullable()->after('id_unit_usaha');
            });
        }

        if (Schema::hasTable('penjualan') && !Schema::hasColumn('penjualan', 'id_project')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->unsignedBigInteger('id_project')->nullable()->after('id_unit_usaha');
            });
        }

        if (Schema::hasTable('pembelian') && !Schema::hasColumn('pembelian', 'id_project')) {
            Schema::table('pembelian', function (Blueprint $table) {
                $table->unsignedBigInteger('id_project')->nullable()->after('id_unit_usaha');
            });
        }

        if (Schema::hasTable('penjualan_penawaran') && !Schema::hasColumn('penjualan_penawaran', 'id_project')) {
            Schema::table('penjualan_penawaran', function (Blueprint $table) {
                $table->unsignedBigInteger('id_project')->nullable()->after('id_unit_usaha');
            });
        }

        if (Schema::hasTable('pembelian_rfq') && !Schema::hasColumn('pembelian_rfq', 'id_project')) {
            Schema::table('pembelian_rfq', function (Blueprint $table) {
                $table->unsignedBigInteger('id_project')->nullable()->after('id_unit_usaha');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pembelian_rfq') && Schema::hasColumn('pembelian_rfq', 'id_project')) {
            Schema::table('pembelian_rfq', function (Blueprint $table) {
                $table->dropColumn('id_project');
            });
        }

        if (Schema::hasTable('penjualan_penawaran') && Schema::hasColumn('penjualan_penawaran', 'id_project')) {
            Schema::table('penjualan_penawaran', function (Blueprint $table) {
                $table->dropColumn('id_project');
            });
        }

        if (Schema::hasTable('pembelian') && Schema::hasColumn('pembelian', 'id_project')) {
            Schema::table('pembelian', function (Blueprint $table) {
                $table->dropColumn('id_project');
            });
        }

        if (Schema::hasTable('penjualan') && Schema::hasColumn('penjualan', 'id_project')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->dropColumn('id_project');
            });
        }

        if (Schema::hasTable('jurnal_umum') && Schema::hasColumn('jurnal_umum', 'id_project')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->dropColumn('id_project');
            });
        }

        Schema::dropIfExists('projects');
    }
};
