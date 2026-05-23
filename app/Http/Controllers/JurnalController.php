<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    use \App\Traits\CheckSaldoTrait;
    use \App\Traits\CheckClosedPeriod;

    public function index()
    {
        $jurnal = Jurnal::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('jurnal.index', compact('jurnal'));
    }

    public function create()
    {
        $akun = Akun::orderBy('kode_akun')->get();
        
        // Generate No Transaksi Otomatis (JU-xxxx)
        $lastJurnal = Jurnal::where('sumber_jurnal', 'Manual')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastJurnal && preg_match('/JU-(\d+)/', $lastJurnal->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'JU-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        return view('jurnal.create', compact('akun', 'noTransaksi', 'cabang', 'unitUsaha', 'pelanggan', 'pemasok', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'deskripsi' => 'required|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'details' => 'required|array|min:2',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.kredit' => 'required|numeric|min:0',
        ]);

        // Validasi Balance
        $totalDebit = collect($request->details)->sum('debit');
        $totalKredit = collect($request->details)->sum('kredit');

        if ($totalDebit != $totalKredit) {
            return back()->with('error', 'Jurnal tidak seimbang (Balance). Total Debit: ' . $totalDebit . ', Total Kredit: ' . $totalKredit)->withInput();
        }

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal);

            // H5: Generate no_transaksi atomik di dalam transaksi
            $lastJurnal = Jurnal::where('sumber_jurnal', 'Manual')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastJurnal && preg_match('/JU-(\d+)/', $lastJurnal->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'JU-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            // Cek Saldo untuk setiap akun yang di-Kredit jika itu adalah Kas & Bank
            foreach ($request->details as $detail) {
                if ($detail['kredit'] > 0) {
                    if (!$this->checkSaldoCukup($detail['kode_akun'], $detail['kredit'])) {
                        $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                        $saldo = $this->getSaldoSaatIni($detail['kode_akun']);
                        throw new \Exception("Saldo akun " . $akun->nama_akun . " tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
                    }
                }
            }

            $fotoBukti = null;
            if ($request->hasFile('foto_bukti')) {
                $foto = $request->file('foto_bukti');
                $filename = 'bukti_jurnal_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/bukti_transaksi', $filename);
                $fotoBukti = $filename;
            }

            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_pelanggan' => $request->id_pelanggan,
                'id_pemasok' => $request->id_pemasok,
                'deskripsi' => $request->deskripsi,
                'sumber_jurnal' => $request->input('sumber_jurnal', 'Manual'),
                'foto_bukti' => $fotoBukti,
                'is_locked' => 0
            ]);

            foreach ($request->details as $detail) {
                if ($detail['debit'] > 0 || $detail['kredit'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $detail['debit'],
                        'kredit' => $detail['kredit'],
                    ]);

                    // Update Saldo Pelanggan/Pemasok jika akun Piutang/Utang
                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun) {
                        if ($akun->tipe_akun == 'Piutang' && $request->id_pelanggan) {
                            $selisih = $detail['debit'] - $detail['kredit'];
                            \App\Models\Pelanggan::where('id_pelanggan', $request->id_pelanggan)->increment('saldo_terkini_piutang', $selisih);
                        } elseif ($akun->tipe_akun == 'Utang Usaha' && $request->id_pemasok) {
                            $selisih = $detail['kredit'] - $detail['debit'];
                            \App\Models\Pemasok::where('id_pemasok', $request->id_pemasok)->increment('saldo_terkini_hutang', $selisih);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal umum berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal menyimpan jurnal. Silakan coba lagi.';
            \Illuminate\Support\Facades\Log::error('Jurnal store error', ['error' => $e->getMessage()]);
            return back()->with('error', $msg)->withInput();
        }
    }

    public function createKas()
    {
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        $akunLawan = Akun::orderBy('kode_akun')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::active()->orderBy('nama_project')->get();

        return view('jurnal.create-kas', compact('akunKas', 'akunLawan', 'cabang', 'unitUsaha', 'projects'));
    }

    public function storeKas(Request $request)
    {
        $request->validate([
            'tipe_transaksi' => 'required|in:masuk,keluar',
            'id_akun_kas' => 'required|exists:akun,kode_akun',
            'tanggal' => 'required|date|before_or_equal:today',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'deskripsi' => 'required|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun',
            'details.*.nominal' => 'required|numeric|min:0',
        ]);

        $prefix = $request->tipe_transaksi === 'masuk' ? 'KM' : 'KK';
        $totalNominal = collect($request->details)->sum('nominal');

        try {
            DB::beginTransaction();
            $this->validatePeriodOpen($request->tanggal);

            // Generate No Transaksi Atomik
            $lastTrans = Jurnal::where('no_transaksi', 'LIKE', $prefix . '-%')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastTrans && preg_match('/' . $prefix . '-(\d+)/', $lastTrans->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = $prefix . '-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            // Cek Saldo jika Kas Keluar
            if ($request->tipe_transaksi === 'keluar') {
                if (!$this->checkSaldoCukup($request->id_akun_kas, $totalNominal)) {
                    $saldo = $this->getSaldoSaatIni($request->id_akun_kas);
                    throw new \Exception("Saldo kas tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
                }
            }

            $fotoBukti = null;
            if ($request->hasFile('foto_bukti')) {
                $foto = $request->file('foto_bukti');
                $filename = 'bukti_jurnal_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/bukti_transaksi', $filename);
                $fotoBukti = $filename;
            }

            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'deskripsi' => $request->deskripsi,
                'sumber_jurnal' => $request->tipe_transaksi === 'masuk' ? 'Kas Masuk' : 'Kas Keluar',
                'foto_bukti' => $fotoBukti,
                'is_locked' => 0
            ]);

            // Detail Akun Kas Utama
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->id_akun_kas,
                'debit' => $request->tipe_transaksi === 'masuk' ? $totalNominal : 0,
                'kredit' => $request->tipe_transaksi === 'keluar' ? $totalNominal : 0,
            ]);

            // Detail Akun Lawan
            foreach ($request->details as $detail) {
                if ($detail['nominal'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $request->tipe_transaksi === 'keluar' ? $detail['nominal'] : 0,
                        'kredit' => $request->tipe_transaksi === 'masuk' ? $detail['nominal'] : 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal Kas berhasil disimpan dengan nomor ' . $noTransaksi);

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal menyimpan jurnal kas. Silakan coba lagi.';
            \Illuminate\Support\Facades\Log::error('Jurnal Kas store error', ['error' => $e->getMessage()]);
            return back()->with('error', $msg)->withInput();
        }
    }

    public function show(Jurnal $jurnal)
    {
        $jurnal->load('details.akun');
        return view('jurnal.show', compact('jurnal'));
    }

    public function edit(Jurnal $jurnal)
    {
        $akun = Akun::orderBy('kode_akun')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();
        $projects = Project::active()->orderBy('nama_project')->get();
        
        return view('jurnal.edit', compact('jurnal', 'akun', 'cabang', 'unitUsaha', 'pelanggan', 'pemasok', 'projects'));
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'id_project' => 'nullable|exists:projects,id_project',
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'deskripsi' => 'required|string',
            'sumber_jurnal' => 'required|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Cek periode tutup buku
            $this->validatePeriodOpen($jurnal->tanggal);
            $this->validatePeriodOpen($request->tanggal);

            // Adjust Pelanggan/Pemasok balance if changed
            if ($jurnal->id_pelanggan != $request->id_pelanggan) {
                $totalPiutang = $jurnal->details()
                    ->whereHas('akun', function($q) { $q->where('tipe_akun', 'Piutang'); })
                    ->select(DB::raw('SUM(debit) - SUM(kredit) as net_change'))
                    ->value('net_change');
                if ($totalPiutang != 0) {
                    if ($jurnal->id_pelanggan) {
                        \App\Models\Pelanggan::where('id_pelanggan', $jurnal->id_pelanggan)->decrement('saldo_terkini_piutang', $totalPiutang);
                    }
                    if ($request->id_pelanggan) {
                        \App\Models\Pelanggan::where('id_pelanggan', $request->id_pelanggan)->increment('saldo_terkini_piutang', $totalPiutang);
                    }
                }
            }

            if ($jurnal->id_pemasok != $request->id_pemasok) {
                $totalHutang = $jurnal->details()
                    ->whereHas('akun', function($q) { $q->where('tipe_akun', 'Utang Usaha'); })
                    ->select(DB::raw('SUM(kredit) - SUM(debit) as net_change'))
                    ->value('net_change');
                if ($totalHutang != 0) {
                    if ($jurnal->id_pemasok) {
                        \App\Models\Pemasok::where('id_pemasok', $jurnal->id_pemasok)->decrement('saldo_terkini_hutang', $totalHutang);
                    }
                    if ($request->id_pemasok) {
                        \App\Models\Pemasok::where('id_pemasok', $request->id_pemasok)->increment('saldo_terkini_hutang', $totalHutang);
                    }
                }
            }

            $fotoBukti = $jurnal->foto_bukti;
            if ($request->hasFile('foto_bukti')) {
                if ($jurnal->foto_bukti) {
                    \Illuminate\Support\Facades\Storage::delete('public/bukti_transaksi/' . $jurnal->foto_bukti);
                }
                $foto = $request->file('foto_bukti');
                $filename = 'bukti_jurnal_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/bukti_transaksi', $filename);
                $fotoBukti = $filename;
            }

            $jurnal->update([
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_pelanggan' => $request->id_pelanggan,
                'id_pemasok' => $request->id_pemasok,
                'deskripsi' => $request->deskripsi,
                'sumber_jurnal' => $request->sumber_jurnal,
                'foto_bukti' => $fotoBukti,
            ]);

            // Sinkronisasi dengan modul terkait
            // Penjualan
            DB::table('penjualan')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal_faktur' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_pelanggan' => $request->id_pelanggan,
                'keterangan' => $request->deskripsi
            ]);

            // Pembelian
            DB::table('pembelian')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal_faktur' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'id_project' => $request->id_project,
                'id_pemasok' => $request->id_pemasok,
                'keterangan' => $request->deskripsi
            ]);
            
            // Simpanan
            DB::table('simpanan')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'keterangan' => $request->deskripsi
            ]);
            
            // Pinjaman Pencairan
            DB::table('pinjaman')->where('id_jurnal_pencairan', $jurnal->id_jurnal)->update([
                'tanggal_pencairan' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'keterangan' => $request->deskripsi
            ]);
            
            // Pinjaman Angsuran
            DB::table('pinjaman_angsuran')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal_bayar' => $request->tanggal,
                'keterangan' => $request->deskripsi
            ]);

            // Produksi (Manufacturing)
            DB::table('produksi')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'keterangan' => $request->deskripsi
            ]);

            // Fixed Assets (Perolehan/Disposal)
            DB::table('fixed_assets')->where('id_jurnal', $jurnal->id_jurnal)->update([
                'tanggal_perolehan' => $request->tanggal,
                'cabang_id' => $request->id_cabang,
            ]);

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $jurnal = Jurnal::findOrFail($id);
            
            if ($jurnal->is_approved) {
                return back()->with('error', 'Jurnal sudah disetujui.');
            }

            // Cek periode tutup buku
            $this->validatePeriodOpen($jurnal->tanggal);

            $jurnal->update(['is_approved' => 1]);

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal ' . $jurnal->no_transaksi . ' berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Jurnal $jurnal)
    {
        try {
            DB::beginTransaction();

            // Cek periode tutup buku
            $this->validatePeriodOpen($jurnal->tanggal);

            // 1. Revert Pelanggan Piutang if linked
            if ($jurnal->id_pelanggan) {
                $totalPiutang = $jurnal->details()
                    ->whereHas('akun', function($q) { $q->where('tipe_akun', 'Piutang'); })
                    ->select(DB::raw('SUM(debit) - SUM(kredit) as net_change'))
                    ->value('net_change');
                if ($totalPiutang != 0) {
                    Pelanggan::where('id_pelanggan', $jurnal->id_pelanggan)->decrement('saldo_terkini_piutang', $totalPiutang);
                }
            }

            // 2. Revert Pemasok Hutang if linked
            if ($jurnal->id_pemasok) {
                $totalHutang = $jurnal->details()
                    ->whereHas('akun', function($q) { $q->where('tipe_akun', 'Utang Usaha'); })
                    ->select(DB::raw('SUM(kredit) - SUM(debit) as net_change'))
                    ->value('net_change');
                if ($totalHutang != 0) {
                    Pemasok::where('id_pemasok', $jurnal->id_pemasok)->decrement('saldo_terkini_hutang', $totalHutang);
                }
            }

            // Sinkronisasi dengan modul terkait (Unpost)
            DB::table('penjualan')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);
            DB::table('pembelian')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);
            DB::table('simpanan')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);
            DB::table('pinjaman')->where('id_jurnal_pencairan', $jurnal->id_jurnal)->update(['id_jurnal_pencairan' => null]);
            DB::table('pinjaman_angsuran')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);
            DB::table('produksi')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);
            DB::table('fixed_assets')->where('id_jurnal', $jurnal->id_jurnal)->update(['id_jurnal' => null]);

            // Hapus berkas fisik foto jika ada
            if ($jurnal->foto_bukti) {
                \Illuminate\Support\Facades\Storage::delete('public/bukti_transaksi/' . $jurnal->foto_bukti);
            }

            // Hapus Detail
            $jurnal->details()->delete();
            // Hapus Header
            $jurnal->delete();

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
