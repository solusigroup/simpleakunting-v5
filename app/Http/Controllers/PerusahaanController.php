<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerusahaanController extends Controller
{
    public function edit()
    {
        // Ambil data perusahaan (asumsi hanya ada 1 record dengan ID 1)
        $perusahaan = DB::table('perusahaan')->find(1);
        
        // Akun list untuk dropdown mapping
        $akun = \App\Models\Akun::orderBy('kode_akun')->get();

        return view('perusahaan.edit', compact('perusahaan', 'akun'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tanggal_mulai_pemakaian' => 'nullable|date',
            'jenis_usaha' => 'required|in:dagang,simpan_pinjam,serba_usaha,jasa,manufaktur,pertanian,multi',
            'nama_direktur' => 'nullable|string|max:255',
            'nama_akuntan' => 'nullable|string|max:255',
            // Account mapping
            'akun_piutang' => 'nullable|string|max:20',
            'akun_utang' => 'nullable|string|max:20',
            'akun_pendapatan' => 'nullable|string|max:20',
            'akun_persediaan' => 'nullable|string|max:20',
            'akun_aset_biologis' => 'nullable|string|max:20',
            'akun_keuntungan_revaluasi' => 'nullable|string|max:20',
            'akun_kerugian_revaluasi' => 'nullable|string|max:20',
            'akun_ikhtisar_laba_rugi' => 'nullable|string|max:20',
            'akun_laba_ditahan' => 'nullable|string|max:20',
            'akun_pendapatan_provisi' => 'nullable|string|max:20',
            'akun_pendapatan_admin' => 'nullable|string|max:20',
            // POS Settings
            'pos_akun_kas_default' => 'nullable|string|max:20',
            'pos_akun_pendapatan_default' => 'nullable|string|max:20',
            'pos_akun_hpp_default' => 'nullable|string|max:20',
            'pos_akun_persediaan_default' => 'nullable|string|max:20',
            'pos_akun_utang_default' => 'nullable|string|max:20',
        ]);

        $oldPerusahaan = DB::table('perusahaan')->find(1);
        $jenisUsahaChanged = !$oldPerusahaan || $oldPerusahaan->jenis_usaha !== $request->jenis_usaha;

        DB::table('perusahaan')->updateOrInsert(
            ['id' => 1],
            [
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'tanggal_mulai_pemakaian' => $request->tanggal_mulai_pemakaian,
                'jenis_usaha' => $request->jenis_usaha,
                'nama_direktur' => $request->nama_direktur,
                'nama_akuntan' => $request->nama_akuntan,
                // Account mapping
                'akun_piutang' => $request->akun_piutang,
                'akun_utang' => $request->akun_utang,
                'akun_pendapatan' => $request->akun_pendapatan,
                'akun_persediaan' => $request->akun_persediaan,
                'akun_aset_biologis' => $request->akun_aset_biologis,
                'akun_keuntungan_revaluasi' => $request->akun_keuntungan_revaluasi,
                'akun_kerugian_revaluasi' => $request->akun_kerugian_revaluasi,
                'akun_ikhtisar_laba_rugi' => $request->akun_ikhtisar_laba_rugi,
                'akun_laba_ditahan' => $request->akun_laba_ditahan,
                'akun_pendapatan_provisi' => $request->akun_pendapatan_provisi,
                'akun_pendapatan_admin' => $request->akun_pendapatan_admin,
                // POS Settings
                'pos_akun_kas_default' => $request->pos_akun_kas_default,
                'pos_akun_pendapatan_default' => $request->pos_akun_pendapatan_default,
                'pos_akun_hpp_default' => $request->pos_akun_hpp_default,
                'pos_akun_persediaan_default' => $request->pos_akun_persediaan_default,
                'pos_akun_utang_default' => $request->pos_akun_utang_default,
                'updated_at' => now(),
            ]
        );

        if ($jenisUsahaChanged) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\CoaTemplateSeeder'
            ]);
        }

        return redirect()->route('perusahaan.edit')->with('success', 'Profil perusahaan berhasil diperbarui' . ($jenisUsahaChanged ? ' dan template COA telah disesuaikan.' : '.'));

    }
}
