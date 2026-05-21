<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    public function index()
    {
        $akun = Akun::orderBy('kode_akun')->get();

        // Hitung saldo terkini per akun dari jurnal_detail
        $saldoPerAkun = DB::table('jurnal_detail')
            ->select('kode_akun', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
            ->groupBy('kode_akun')
            ->get()
            ->keyBy('kode_akun');

        foreach ($akun as $a) {
            $data = $saldoPerAkun->get($a->kode_akun);
            $totalDebit = $data->total_debit ?? 0;
            $totalKredit = $data->total_kredit ?? 0;

            if ($a->saldo_normal == 'Debit') {
                $a->saldo_terkini = $totalDebit - $totalKredit;
            } else {
                $a->saldo_terkini = $totalKredit - $totalDebit;
            }
        }

        return view('akun.index', compact('akun'));
    }

    public function create()
    {
        return view('akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|unique:akun,kode_akun|max:20',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['saldo_awal'] = $request->input('saldo_awal', 0);
        Akun::create($data);

        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Akun $akun)
    {
        return view('akun.edit', compact('akun'));
    }

    public function update(Request $request, Akun $akun)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric|min:0',
        ]);

        $akun->update($request->all());

        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Akun $akun)
    {
        $isUsed = \Illuminate\Support\Facades\DB::table('jurnal_detail')->where('kode_akun', $akun->kode_akun)->exists();
        if ($isUsed) {
            return back()->with('error', 'Gagal menghapus: Akun ini sudah digunakan dalam jurnal transaksi.');
        }

        try {
            $akun->delete();
            return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}
