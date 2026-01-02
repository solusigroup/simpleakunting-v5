<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cabang (Branches)
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cabang')->unique();
            $table->string('nama_cabang');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        // 2. Add id_cabang to Users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cabang')->nullable()->after('role');
            // $table->foreign('id_cabang')->references('id')->on('cabang'); // Constraint optional for now to avoid issues with existing data
        });

        // 3. Update Perusahaan (Business Types)
        // Modify enum is DB specific, using raw statement for MySQL
        DB::statement("ALTER TABLE perusahaan MODIFY COLUMN jenis_usaha ENUM('dagang', 'simpan_pinjam', 'serba_usaha', 'jasa', 'manufaktur', 'pertanian', 'multi') DEFAULT 'dagang'");

        // 4. Manufacturing Tables
        Schema::create('bom', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bom')->unique();
            $table->string('nama_bom');
            $table->unsignedBigInteger('barang_jadi_id'); // ID of the finished good in master_persediaan
            $table->decimal('kuantitas_hasil', 10, 2)->default(1);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('bom_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('material_id'); // ID of raw material in master_persediaan
            $table->decimal('kuantitas', 10, 4);
            $table->string('satuan')->nullable();
            $table->timestamps();
            
            $table->foreign('bom_id')->references('id')->on('bom')->onDelete('cascade');
        });

        Schema::create('produksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_produksi')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('id_cabang')->nullable();
            $table->decimal('kuantitas_produksi', 10, 2);
            $table->string('status')->default('draft'); // draft, process, completed
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('produksi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produksi_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('kuantitas_digunakan', 10, 4);
            $table->decimal('biaya_satuan', 15, 2)->default(0); // Cost at time of production
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
        });

        // 5. Agriculture (PSAK 69 - Biological Assets)
        Schema::create('aset_biologis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->enum('jenis', ['tanaman', 'hewan']);
            $table->date('tanggal_perolehan');
            $table->integer('umur_bulan')->default(0);
            $table->string('lokasi')->nullable();
            $table->decimal('nilai_perolehan', 15, 2);
            $table->decimal('nilai_wajar', 15, 2); // Fair Value
            $table->decimal('estimasi_biaya_jual', 15, 2)->default(0); 
            $table->unsignedBigInteger('id_cabang')->nullable();
            $table->string('status')->default('aktif'); // aktif, panen, mati, dijual
            $table->timestamps();
        });

        Schema::create('log_revaluasi_aset', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aset_biologis_id');
            $table->date('tanggal_revaluasi');
            $table->decimal('nilai_buku_sebelum', 15, 2);
            $table->decimal('nilai_wajar_baru', 15, 2);
            $table->decimal('selisih_nilai', 15, 2); // Gain/Loss
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('aset_biologis_id')->references('id')->on('aset_biologis')->onDelete('cascade');
        });

        // 6. Book Closing (Tutup Buku)
        Schema::create('periode_tutup_buku', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->date('tanggal_tutup');
            $table->unsignedBigInteger('user_id'); // Who closed it
            $table->enum('status', ['tutup', 'buka_kembali'])->default('tutup');
            $table->string('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['bulan', 'tahun']);
        });

        Schema::create('ikhtisar_laba_rugi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periode_id');
            $table->decimal('total_pendapatan', 15, 2);
            $table->decimal('total_beban', 15, 2);
            $table->decimal('laba_rugi_bersih', 15, 2);
            $table->timestamps();
             
             $table->foreign('periode_id')->references('id')->on('periode_tutup_buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikhtisar_laba_rugi');
        Schema::dropIfExists('periode_tutup_buku');
        Schema::dropIfExists('log_revaluasi_aset');
        Schema::dropIfExists('aset_biologis');
        Schema::dropIfExists('produksi_detail');
        Schema::dropIfExists('produksi');
        Schema::dropIfExists('bom_detail');
        Schema::dropIfExists('bom');
        
        // Revert enum change (approximation)
        DB::statement("ALTER TABLE perusahaan MODIFY COLUMN jenis_usaha ENUM('dagang', 'simpan_pinjam', 'serba_usaha', 'jasa') DEFAULT 'dagang'");
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_cabang');
        });
        
        Schema::dropIfExists('cabang');
    }
};
