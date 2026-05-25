<?php

namespace App\Http\Controllers;

use App\Models\PenjualanPenawaran;
use App\Models\PenjualanPenawaranDetail;
use App\Models\Pelanggan;
use App\Models\Persediaan;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanPenawaranController extends Controller
{
    public function index()
    {
        $penawaran = PenjualanPenawaran::with('pelanggan')
            ->orderBy('tanggal_penawaran', 'desc')
            ->orderBy('id_penawaran', 'desc')
            ->paginate(20);
            
        return view('penjualan.penawaran.index', compact('penawaran'));
    }

    public function create()
    {
        $pelanggan = Pelanggan::all();
        $barang = Persediaan::all();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        // Generate QTN number
        $lastQtn = PenjualanPenawaran::orderBy('id_penawaran', 'desc')->first();
        $nextNo = $lastQtn ? (int)substr($lastQtn->no_penawaran, 4) + 1 : 1;
        $noPenawaran = 'QTN-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('penjualan.penawaran.create', compact('pelanggan', 'barang', 'cabang', 'unitUsaha', 'noPenawaran', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'tanggal_penawaran' => 'required|date',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate atomic number
            $lastQtn = PenjualanPenawaran::orderBy('id_penawaran', 'desc')->lockForUpdate()->first();
            $nextNo = $lastQtn ? (int)substr($lastQtn->no_penawaran, 4) + 1 : 1;
            $noPenawaran = 'QTN-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $total = 0;
            $details = [];

            foreach ($request->details as $item) {
                $subtotal = $item['kuantitas'] * $item['harga'];
                $total += $subtotal;
                $details[] = [
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal
                ];
            }

            $penawaran = PenjualanPenawaran::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'no_penawaran' => $noPenawaran,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'total' => $total,
                'keterangan' => $request->keterangan,
                'status' => 'Draft'
            ]);

            foreach ($details as $detail) {
                $detail['id_penawaran'] = $penawaran->id_penawaran;
                PenjualanPenawaranDetail::create($detail);
            }

            DB::commit();
            return redirect()->route('penawaran.index')->with('success', 'Penawaran penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store penawaran error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penawaran: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $penawaran = PenjualanPenawaran::with(['details.barang', 'pelanggan', 'cabang', 'unitUsaha'])
            ->findOrFail($id);
            
        return view('penjualan.penawaran.show', compact('penawaran'));
    }

    public function edit($id)
    {
        $penawaran = PenjualanPenawaran::with('details.barang')->findOrFail($id);
        if ($penawaran->status === 'Dikonversi') {
            return redirect()->route('penawaran.index')->with('error', 'Penawaran yang telah dikonversi tidak dapat diedit.');
        }

        $pelanggan = Pelanggan::all();
        $barang = Persediaan::all();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        return view('penjualan.penawaran.edit', compact('penawaran', 'pelanggan', 'barang', 'cabang', 'unitUsaha', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $penawaran = PenjualanPenawaran::findOrFail($id);
        if ($penawaran->status === 'Dikonversi') {
            return redirect()->route('penawaran.index')->with('error', 'Penawaran yang telah dikonversi tidak dapat diupdate.');
        }

        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'tanggal_penawaran' => 'required|date',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:Draft,Dikirim,Diterima,Ditolak',
            'details' => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:master_persediaan,id_barang',
            'details.*.kuantitas' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $details = [];

            foreach ($request->details as $item) {
                $subtotal = $item['kuantitas'] * $item['harga'];
                $total += $subtotal;
                $details[] = [
                    'id_barang' => $item['id_barang'],
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal
                ];
            }

            $penawaran->update([
                'id_pelanggan' => $request->id_pelanggan,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'total' => $total,
                'keterangan' => $request->keterangan,
                'status' => $request->status
            ]);

            // Hapus detail lama dan masukkan yang baru
            $penawaran->details()->delete();

            foreach ($details as $detail) {
                $detail['id_penawaran'] = $penawaran->id_penawaran;
                PenjualanPenawaranDetail::create($detail);
            }

            DB::commit();
            return redirect()->route('penawaran.show', $penawaran->id_penawaran)->with('success', 'Penawaran penjualan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update penawaran error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui penawaran: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $penawaran = PenjualanPenawaran::findOrFail($id);
        if ($penawaran->status === 'Dikonversi') {
            return redirect()->route('penawaran.index')->with('error', 'Penawaran yang telah dikonversi tidak dapat dihapus.');
        }

        try {
            DB::beginTransaction();
            $penawaran->details()->delete();
            $penawaran->delete();
            DB::commit();
            
            return redirect()->route('penawaran.index')->with('success', 'Penawaran penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete penawaran error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus penawaran.');
        }
    }

    public function convertToInvoice($id)
    {
        $penawaran = PenjualanPenawaran::findOrFail($id);
        if ($penawaran->status === 'Dikonversi') {
            return back()->with('error', 'Penawaran ini sudah dikonversi sebelumnya.');
        }
        
        return redirect()->route('penjualan.create', ['from_penawaran' => $penawaran->id_penawaran]);
    }

    public function cetak($id)
    {
        $penawaran = PenjualanPenawaran::with(['details.barang', 'pelanggan'])->findOrFail($id);
        $perusahaan = \App\Models\Tenant::find(tenant('id'));
        return view('penjualan.penawaran.cetak', compact('penawaran', 'perusahaan'));
    }
}
