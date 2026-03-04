<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->string('akun_piutang', 20)->nullable()->after('jenis_usaha');
            $table->string('akun_utang', 20)->nullable()->after('akun_piutang');
            $table->string('akun_pendapatan', 20)->nullable()->after('akun_utang');
            $table->string('akun_persediaan', 20)->nullable()->after('akun_pendapatan');
        });

        // Set defaults based on common BUMDes/SME COA patterns
        \Illuminate\Support\Facades\DB::table('perusahaan')->update([
            'akun_piutang' => '1-10100',
            'akun_utang' => '2-10100',
            'akun_pendapatan' => '4-10000',
            'akun_persediaan' => '1-10200',
        ]);
    }

    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropColumn(['akun_piutang', 'akun_utang', 'akun_pendapatan', 'akun_persediaan']);
        });
    }
};
