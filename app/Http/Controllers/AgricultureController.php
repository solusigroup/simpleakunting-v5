<?php

namespace App\Http\Controllers;

use App\Models\AsetBiologis;
use App\Models\LogRevaluasiAset;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AgricultureController extends Controller
{
    public function index()
    {
        $assets = AsetBiologis::with('cabang')->orderBy('nama_aset')->get();
        return view('agriculture.index', compact('assets'));
    }

    public function create()
    {
        $cabang = Cabang::all();
        // Assuming we might have a dynamic way to set initial fair value or cost
        return view('agriculture.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_aset' => 'required|string',
            'jenis' => 'required|in:tanaman,hewan',
            'tanggal_perolehan' => 'required|date',
            'nilai_perolehan' => 'required|numeric|min:0',
            'nilai_wajar' => 'required|numeric|min:0',
            'id_cabang' => 'nullable|exists:cabang,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Asset
            // Generate Code (e.g., BIO-0001)
            $lastAsset = AsetBiologis::latest('id')->first();
            $nextId = $lastAsset ? $lastAsset->id + 1 : 1;
            $kodeAset = 'BIO-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $asset = AsetBiologis::create([
                'kode_aset' => $kodeAset,
                'nama_aset' => $request->nama_aset,
                'jenis' => $request->jenis,
                'tanggal_perolehan' => $request->tanggal_perolehan,
                'nilai_perolehan' => $request->nilai_perolehan,
                'nilai_wajar' => $request->nilai_wajar, // Initial fair value usually equals cost
                'estimasi_biaya_jual' => $request->estimasi_biaya_jual ?? 0,
                'id_cabang' => $request->id_cabang,
                'lokasi' => $request->lokasi,
                'umur_bulan' => $request->umur_bulan ?? 0,
            ]);

            // 2. Journal Entry for Acquisition (Purchase)
            // Debit: Biological Asset
            // Credit: Cash/Bank (Assuming Cash for simplicity or user selects)
            // Ideally user chooses the Credit Account. For now, hardcoding or we need input.
            // Let's assume this is just master data entry if no financial interaction is requested, 
            // but PSAK 69 implies valid accounting.
            // I'll skip auto-journal for "Store" unless requested, often initial data migration doesn't need journal.
            // But if it's a new purchase, it should. I'll add a 'create_journal' flag or similar in future. 
            // For now, let's assume it's just recording the asset. 

            DB::commit();
            return redirect()->route('agriculture.index')->with('success', 'Aset biologis berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan aset: ' . $e->getMessage())->withInput();
        }
    }

    public function revaluation(Request $request, $id)
    {
        $request->validate([
            'nilai_wajar_baru' => 'required|numeric|min:0',
            'tanggal_revaluasi' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $asset = AsetBiologis::findOrFail($id);
            $oldFairValue = $asset->nilai_wajar;
            $newFairValue = $request->nilai_wajar_baru;
            $selisih = $newFairValue - $oldFairValue;

            if ($selisih == 0) {
                return back()->with('info', 'Nilai wajar tidak berubah.');
            }

            // 1. Update Asset
            $asset->nilai_wajar = $newFairValue;
            $asset->save();

            // 2. Log Revaluation
            LogRevaluasiAset::create([
                'aset_biologis_id' => $asset->id,
                'tanggal_revaluasi' => $request->tanggal_revaluasi,
                'nilai_buku_sebelum' => $oldFairValue,
                'nilai_wajar_baru' => $newFairValue,
                'selisih_nilai' => $selisih,
                'keterangan' => $request->keterangan,
            ]);

            // 3. Create Journal Entry (PSAK 69)
            // Gain/Loss on Fair Value Changes
            // Gain: Debit Bio Asset, Credit Gain (Revenue)
            // Loss: Debit Loss (Expense), Credit Bio Asset
            
            // Hardcoded Accounts for now (Needs to be in Settings)
            $akunAsetBiologis = '1-10500'; // Example
            $akunKeuntungan = '4-80000'; // Other Income
            $akunKerugian = '6-80000'; // Other Expense

            // Generate No Transaksi
            $lastJurnal = Jurnal::where('sumber_jurnal', 'Revaluasi')->orderBy('id_jurnal', 'desc')->first();
            $nextNo = 1;
             if ($lastJurnal && preg_match('/REV-(\d+)/', $lastJurnal->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'REV-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal_revaluasi,
                'deskripsi' => "Revaluasi Aset Biologis {$asset->nama_aset}",
                'sumber_jurnal' => 'Revaluasi',
            ]);

            if ($selisih > 0) {
                // Gain
                JurnalDetail::create(['id_jurnal' => $jurnal->id_jurnal, 'kode_akun' => $akunAsetBiologis, 'debit' => $selisih, 'kredit' => 0]);
                JurnalDetail::create(['id_jurnal' => $jurnal->id_jurnal, 'kode_akun' => $akunKeuntungan, 'debit' => 0, 'kredit' => $selisih]);
            } else {
                // Loss (selisih is negative)
                $lossAmount = abs($selisih);
                JurnalDetail::create(['id_jurnal' => $jurnal->id_jurnal, 'kode_akun' => $akunKerugian, 'debit' => $lossAmount, 'kredit' => 0]);
                JurnalDetail::create(['id_jurnal' => $jurnal->id_jurnal, 'kode_akun' => $akunAsetBiologis, 'debit' => 0, 'kredit' => $lossAmount]);
            }

            DB::commit();
            return back()->with('success', 'Revaluasi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan revaluasi: ' . $e->getMessage());
        }
    }
}
