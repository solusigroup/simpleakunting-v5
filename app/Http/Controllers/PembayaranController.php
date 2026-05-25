<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pemasok;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    use \App\Traits\CheckSaldoTrait;
    use \App\Traits\CheckClosedPeriod;

    public function index()
    {
        $pembayaran = Jurnal::with(['cabang', 'unitUsaha', 'details'])
            ->where('sumber_jurnal', 'Pengeluaran Kas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create(Request $request)
    {
        // Akun Kas/Bank untuk Kredit
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Akun Beban/Utang untuk Debit
        $akunBeban = Akun::whereIn('tipe_akun', ['Beban', 'Beban Lainnya', 'Utang Usaha', 'Kewajiban Lancar Lainnya'])->orderBy('kode_akun')->get();
        
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();

        // Generate No Transaksi (CD-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Pengeluaran Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/CD-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'CD-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::orderBy('nama_unit')->get();

        return view('pembayaran.create', compact('akunKas', 'akunBeban', 'pemasok', 'noTransaksi', 'cabang', 'unitUsaha'));
    }

    public function store(Request $request)
    {
        // Hapus baris detail yang kosong (tidak dipilih akunnya dan nominal 0)
        if ($request->has('details') && is_array($request->details)) {
            $filteredDetails = array_filter($request->details, function ($detail) {
                return !empty($detail['kode_akun']) || (!empty($detail['jumlah']) && $detail['jumlah'] > 0);
            });
            $request->merge(['details' => array_values($filteredDetails)]);
        }

        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'akun_kas' => 'required|exists:akun,kode_akun', // Kredit
            'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'nullable|exists:unit_usaha,id',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun', // Debit
            'details.*.jumlah' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal);

            // H5: Generate no_transaksi atomik
            $lastTrans = Jurnal::where('sumber_jurnal', 'Pengeluaran Kas')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastTrans && preg_match('/CD-(\d+)/', $lastTrans->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'CD-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $totalBayar = collect($request->details)->sum('jumlah');

            // Cek Saldo
            if (!$this->checkSaldoCukup($request->akun_kas, $totalBayar)) {
                $saldo = $this->getSaldoSaatIni($request->akun_kas);
                throw new \Exception("Saldo tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
            }

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'id_pemasok' => $request->id_pemasok,
                'sumber_jurnal' => 'Pengeluaran Kas',
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'is_locked' => 0
            ]);

            // 2. Jurnal Detail: Kredit Kas/Bank
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas,
                'debit' => 0,
                'kredit' => $totalBayar
            ]);

            // 3. Jurnal Detail: Debit Akun Lawan (Beban/Utang)
            foreach ($request->details as $detail) {
                if ($detail['jumlah'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $detail['jumlah'],
                        'kredit' => 0
                    ]);

                    // Jika akun Utang dan ada Pemasok, update saldo utang pemasok
                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun && $akun->tipe_akun == 'Utang Usaha' && $request->id_pemasok) {
                        $pemasok = Pemasok::find($request->id_pemasok);
                        $pemasok->saldo_terkini_hutang -= $detail['jumlah'];
                        $pemasok->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('pembayaran.index')->with('success', 'Pengeluaran kas berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal menyimpan transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function show($id)
    {
        $jurnal = Jurnal::with(['details.akun', 'cabang', 'unitUsaha'])->findOrFail($id);
        return view('pembayaran.show', compact('jurnal'));
    }

    public function edit($id)
    {
        $jurnal = Jurnal::with('details')->findOrFail($id);
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        $akunBiaya = Akun::whereIn('tipe_akun', ['Beban', 'Beban Pokok Penjualan', 'Utang Usaha'])->orderBy('kode_akun')->get();
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::orderBy('nama_unit')->get();

        // Get kredit account (Kas)
        $detailKas = $jurnal->details->where('kredit', '>', 0)->first();
        $akunKasId = $detailKas ? $detailKas->kode_akun : '';

        // Get debit accounts
        $detailsDebit = $jurnal->details->where('debit', '>', 0)->values();

        return view('pembayaran.edit', compact('jurnal', 'akunKas', 'akunBiaya', 'pemasok', 'cabang', 'unitUsaha', 'akunKasId', 'detailsDebit'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('details') && is_array($request->details)) {
            $filteredDetails = array_filter($request->details, function ($detail) {
                return !empty($detail['kode_akun']) || (!empty($detail['jumlah']) && $detail['jumlah'] > 0);
            });
            $request->merge(['details' => array_values($filteredDetails)]);
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'akun_kas' => 'required|exists:akun,kode_akun',
            'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'nullable|exists:unit_usaha,id',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun',
            'details.*.jumlah' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $jurnal = Jurnal::findOrFail($id);
            $this->validatePeriodOpen($jurnal->tanggal);
            $this->validatePeriodOpen($request->tanggal);

            $totalBayar = collect($request->details)->sum('jumlah');

            // Cek Saldo
            $detailKasLama = $jurnal->details->where('kredit', '>', 0)->first();
            $jumlahKasLama = $detailKasLama ? $detailKasLama->kredit : 0;
            $akunKasLama = $detailKasLama ? $detailKasLama->kode_akun : null;

            // Jika akun kas berubah atau jumlah bertambah, cek saldo
            if ($request->akun_kas != $akunKasLama) {
                if (!$this->checkSaldoCukup($request->akun_kas, $totalBayar)) {
                    $saldo = $this->getSaldoSaatIni($request->akun_kas);
                    throw new \Exception("Saldo tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
                }
            } else {
                $selisih = $totalBayar - $jumlahKasLama;
                if ($selisih > 0 && !$this->checkSaldoCukup($request->akun_kas, $selisih)) {
                    $saldo = $this->getSaldoSaatIni($request->akun_kas);
                    throw new \Exception("Saldo tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
                }
            }

            // Reverse old impacts
            foreach ($jurnal->details as $detail) {
                if ($detail->debit > 0) {
                    $akun = Akun::where('kode_akun', $detail->kode_akun)->first();
                    if ($akun && $akun->tipe_akun == 'Utang Usaha' && $jurnal->id_pemasok) {
                        $pemasok = Pemasok::find($jurnal->id_pemasok);
                        if ($pemasok) {
                            $pemasok->saldo_terkini_hutang += $detail->debit;
                            $pemasok->save();
                        }
                    }
                }
            }
            JurnalDetail::where('id_jurnal', $jurnal->id_jurnal)->delete();

            // Apply new details
            $jurnal->update([
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'id_pemasok' => $request->id_pemasok,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
            ]);

            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas,
                'debit' => 0,
                'kredit' => $totalBayar
            ]);

            foreach ($request->details as $detail) {
                if ($detail['jumlah'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $detail['jumlah'],
                        'kredit' => 0
                    ]);

                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun && $akun->tipe_akun == 'Utang Usaha' && $request->id_pemasok) {
                        $pemasok = Pemasok::find($request->id_pemasok);
                        if ($pemasok) {
                            $pemasok->saldo_terkini_hutang -= $detail['jumlah'];
                            $pemasok->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('pembayaran.index')->with('success', 'Pengeluaran kas berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal memperbarui transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $jurnal = Jurnal::findOrFail($id);
            $this->validatePeriodOpen($jurnal->tanggal);

            // Reverse impacts
            foreach ($jurnal->details as $detail) {
                if ($detail->debit > 0) {
                    $akun = Akun::where('kode_akun', $detail->kode_akun)->first();
                    if ($akun && $akun->tipe_akun == 'Utang Usaha' && $jurnal->id_pemasok) {
                        $pemasok = Pemasok::find($jurnal->id_pemasok);
                        if ($pemasok) {
                            $pemasok->saldo_terkini_hutang += $detail->debit;
                            $pemasok->save();
                        }
                    }
                }
            }
            
            JurnalDetail::where('id_jurnal', $jurnal->id_jurnal)->delete();
            $jurnal->delete();

            DB::commit();
            return redirect()->route('pembayaran.index')->with('success', 'Pengeluaran kas berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') ? $e->getMessage() : 'Gagal menghapus transaksi. Silakan coba lagi.';
            return back()->with('error', $msg);
        }
    }
}
