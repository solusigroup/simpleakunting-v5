<?php

namespace App\Http\Controllers;

use App\Models\PembelianRfq;
use App\Models\PembelianRfqDetail;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianRfqController extends Controller
{
    public function index()
    {
        $rfq = PembelianRfq::with('pemasok')
            ->orderBy('tanggal_rfq', 'desc')
            ->orderBy('id_rfq', 'desc')
            ->paginate(20);
            
        return view('pembelian.rfq.index', compact('rfq'));
    }

    public function create()
    {
        $pemasok = Pemasok::all();
        $barang = Persediaan::all();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        // Generate RFQ number
        $lastRfq = PembelianRfq::orderBy('id_rfq', 'desc')->first();
        $nextNo = $lastRfq ? (int)substr($lastRfq->no_rfq, 4) + 1 : 1;
        $noRfq = 'RFQ-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('pembelian.rfq.create', compact('pemasok', 'barang', 'cabang', 'unitUsaha', 'noRfq', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'tanggal_rfq' => 'required|date',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate atomic number
            $lastRfq = PembelianRfq::orderBy('id_rfq', 'desc')->lockForUpdate()->first();
            $nextNo = $lastRfq ? (int)substr($lastRfq->no_rfq, 4) + 1 : 1;
            $noRfq = 'RFQ-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $total = 0;
            $details = [];

            foreach ($request->details as $item) {
                $subtotal = $item['kuantitas'] * $item['harga_beli'];
                $total += $subtotal;
                $details[] = [
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal
                ];
            }

            $rfq = PembelianRfq::create([
                'id_pemasok' => $request->id_pemasok,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'no_rfq' => $noRfq,
                'tanggal_rfq' => $request->tanggal_rfq,
                'total' => $total,
                'keterangan' => $request->keterangan,
                'status' => 'Draft'
            ]);

            foreach ($details as $detail) {
                $detail['id_rfq'] = $rfq->id_rfq;
                PembelianRfqDetail::create($detail);
            }

            DB::commit();
            return redirect()->route('rfq.index')->with('success', 'RFQ Pembelian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store RFQ error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan RFQ: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $rfq = PembelianRfq::with(['details.barang', 'pemasok', 'cabang', 'unitUsaha'])
            ->findOrFail($id);
            
        return view('pembelian.rfq.show', compact('rfq'));
    }

    public function edit($id)
    {
        $rfq = PembelianRfq::with('details.barang')->findOrFail($id);
        if ($rfq->status === 'Dikonversi') {
            return redirect()->route('rfq.index')->with('error', 'RFQ yang telah dikonversi tidak dapat diedit.');
        }

        $pemasok = Pemasok::all();
        $barang = Persediaan::all();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        return view('pembelian.rfq.edit', compact('rfq', 'pemasok', 'barang', 'cabang', 'unitUsaha', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $rfq = PembelianRfq::findOrFail($id);
        if ($rfq->status === 'Dikonversi') {
            return redirect()->route('rfq.index')->with('error', 'RFQ yang telah dikonversi tidak dapat diupdate.');
        }

        $request->validate([
            'id_pemasok' => 'required|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'tanggal_rfq' => 'required|date',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:Draft,Dikirim,Disetujui',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $details = [];

            foreach ($request->details as $item) {
                $subtotal = $item['kuantitas'] * $item['harga_beli'];
                $total += $subtotal;
                $details[] = [
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga_beli'],
                    'subtotal' => $subtotal
                ];
            }

            $rfq->update([
                'id_pemasok' => $request->id_pemasok,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'tanggal_rfq' => $request->tanggal_rfq,
                'total' => $total,
                'keterangan' => $request->keterangan,
                'status' => $request->status
            ]);

            // Hapus detail lama dan masukkan yang baru
            $rfq->details()->delete();

            foreach ($details as $detail) {
                $detail['id_rfq'] = $rfq->id_rfq;
                PembelianRfqDetail::create($detail);
            }

            DB::commit();
            return redirect()->route('rfq.show', $rfq->id_rfq)->with('success', 'RFQ Pembelian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update RFQ error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui RFQ: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $rfq = PembelianRfq::findOrFail($id);

        try {
            DB::beginTransaction();
            $rfq->details()->delete();
            $rfq->delete();
            DB::commit();
            
            return redirect()->route('rfq.index')->with('success', 'RFQ Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete RFQ error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus RFQ.');
        }
    }

    public function convertToPurchase($id)
    {
        $rfq = PembelianRfq::findOrFail($id);
        if ($rfq->status === 'Dikonversi') {
            return back()->with('error', 'RFQ ini sudah dikonversi sebelumnya.');
        }
        
        return redirect()->route('pembelian.create', ['from_rfq' => $rfq->id_rfq]);
    }

    public function cetak($id)
    {
        $rfq = PembelianRfq::with(['details.barang', 'pemasok', 'cabang', 'unitUsaha'])->findOrFail($id);
        $perusahaan = \App\Models\Tenant::find(tenant('id'));
        return view('pembelian.rfq.cetak', compact('rfq', 'perusahaan'));
    }
}
