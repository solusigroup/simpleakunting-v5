<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedAsset;
use App\Models\FixedAssetDisposal;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Carbon\Carbon;

class FixedAssetDisposalController extends Controller
{
    public function create($asset_id)
    {
        $asset = FixedAsset::with('group')->findOrFail($asset_id);
        
        if ($asset->status !== 'Aktif') {
            return redirect()->route('aset-tetap.index')->with('error', 'Aset sudah tidak aktif (sudah dilepas/dijual).');
        }

        $kasAkuns = Akun::where('tipe_akun', 'Kas & Bank')->get();
        $labaRugiAkuns = Akun::whereIn('tipe_akun', ['Pendapatan Lainnya', 'Beban Lainnya'])->get();

        return view('aset-tetap.disposal.create', compact('asset', 'kasAkuns', 'labaRugiAkuns'));
    }

    public function store(Request $request, $asset_id)
    {
        $asset = FixedAsset::with('group')->findOrFail($asset_id);

        if ($asset->status !== 'Aktif') {
            return redirect()->route('aset-tetap.index')->with('error', 'Aset sudah tidak aktif.');
        }

        $request->validate([
            'tanggal_pelepasan' => 'required|date',
            'jenis_pelepasan' => 'required|string|in:Dijual,Dibuang,Rusak',
            'harga_jual' => 'required_if:jenis_pelepasan,Dijual|numeric|min:0',
            'akun_kas' => 'required_if:jenis_pelepasan,Dijual',
            'akun_laba_rugi_pelepasan' => 'required|exists:akun,kode_akun'
        ]);

        $group = $asset->group;
        if (!$group || !$group->akun_aset || !$group->akun_akumulasi_penyusutan) {
            return redirect()->back()->with('error', 'Kelompok aset tidak memiliki pemetaan akun (Aset dan Akumulasi) yang lengkap untuk jurnal pelepasan.');
        }

        $tanggal = $request->tanggal_pelepasan;
        $jenis = $request->jenis_pelepasan;
        $hargaJual = $jenis === 'Dijual' ? ($request->harga_jual ?? 0) : 0;
        
        $akumulasiSusut = $asset->harga_perolehan - $asset->nilai_buku_saat_ini;
        $nilaiBuku = $asset->nilai_buku_saat_ini;
        $labaRugi = $hargaJual - $nilaiBuku; // Positif: Laba, Negatif: Rugi

        $jurnalId = null;

        // Auto Journal
        // D: Kas (sebesar harga_jual)
        // D: Akumulasi Penyusutan (sebesar akumulasiSusut)
        // K: Aset Tetap (sebesar harga_perolehan)
        // Selisih: Laba di Kredit, Rugi di Debit
        
        $lastJurnal = Jurnal::orderBy('id_jurnal', 'desc')->first();
        $nextId = $lastJurnal ? $lastJurnal->id_jurnal + 1 : 1;
        $noTransaksi = 'DS-' . date('Ymd', strtotime($tanggal)) . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $jurnal = Jurnal::create([
            'no_transaksi' => $noTransaksi,
            'tanggal' => $tanggal,
            'deskripsi' => 'Pelepasan Aset Tetap: ' . $asset->nama_aset . ' (' . $jenis . ')',
            'sumber_jurnal' => 'Pelepasan Aset',
            'is_locked' => 0,
            'cabang_id' => $asset->cabang_id
        ]);

        $jurnalId = $jurnal->id_jurnal;

        // D: Kas
        if ($hargaJual > 0 && $request->akun_kas) {
            JurnalDetail::create([
                'id_jurnal' => $jurnalId,
                'kode_akun' => $request->akun_kas,
                'debit' => $hargaJual,
                'kredit' => 0,
            ]);
        }

        // D: Akumulasi Penyusutan
        if ($akumulasiSusut > 0) {
            JurnalDetail::create([
                'id_jurnal' => $jurnalId,
                'kode_akun' => $group->akun_akumulasi_penyusutan,
                'debit' => $akumulasiSusut,
                'kredit' => 0,
            ]);
        }

        // K: Aset Tetap
        JurnalDetail::create([
            'id_jurnal' => $jurnalId,
            'kode_akun' => $group->akun_aset,
            'debit' => 0,
            'kredit' => $asset->harga_perolehan,
        ]);

        // Laba / Rugi Pelepasan
        if ($labaRugi != 0) {
            JurnalDetail::create([
                'id_jurnal' => $jurnalId,
                'kode_akun' => $request->akun_laba_rugi_pelepasan,
                'debit' => $labaRugi < 0 ? abs($labaRugi) : 0,   // Rugi di Debit
                'kredit' => $labaRugi > 0 ? $labaRugi : 0,        // Laba di Kredit
            ]);
        }

        // Save disposal record
        FixedAssetDisposal::create([
            'aset_id' => $asset->id,
            'tanggal_pelepasan' => $tanggal,
            'jenis_pelepasan' => $jenis,
            'harga_jual' => $hargaJual,
            'akun_kas' => $request->akun_kas,
            'akun_laba_rugi_pelepasan' => $request->akun_laba_rugi_pelepasan,
            'jurnal_id' => $jurnalId
        ]);

        // Update asset status and set book value to 0
        $asset->status = $jenis;
        $asset->nilai_buku_saat_ini = 0;
        $asset->save();

        return redirect()->route('aset-tetap.index')->with('success', 'Pelepasan aset berhasil dicatat.');
    }
}
