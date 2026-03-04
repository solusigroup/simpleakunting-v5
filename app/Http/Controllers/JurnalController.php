<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
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

        return view('jurnal.create', compact('akun', 'noTransaksi', 'cabang', 'unitUsaha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'deskripsi' => 'required|string',
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

            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'deskripsi' => $request->deskripsi,
                'sumber_jurnal' => 'Manual',
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

    public function show(Jurnal $jurnal)
    {
        $jurnal->load('details.akun');
        return view('jurnal.show', compact('jurnal'));
    }
}
