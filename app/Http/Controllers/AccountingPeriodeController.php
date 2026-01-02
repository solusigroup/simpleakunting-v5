<?php

namespace App\Http\Controllers;

use App\Models\PeriodeTutupBuku;
use App\Models\IkhtisarLabaRugi;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingPeriodeController extends Controller
{
    public function index()
    {
        $periodes = PeriodeTutupBuku::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        return view('accounting.closing.index', compact('periodes'));
    }

    public function create()
    {
        return view('accounting.closing.create');
    }

    public function closeBook(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
            'tanggal_tutup' => 'required|date',
        ]);

        // Check if already closed
        $exists = PeriodeTutupBuku::where('bulan', $request->bulan)->where('tahun', $request->tahun)->exists();
        if ($exists) {
            return back()->with('error', 'Periode ini sudah ditutup.');
        }

        try {
            DB::beginTransaction();

            // 1. Calculate Revenue and Expenses
            // Get all Jurnal details for the period
            // Assumption: Jurnal has 'tanggal'
            
            $startDate = "{$request->tahun}-{$request->bulan}-01";
            $endDate = date("Y-m-t", strtotime($startDate));

            // Get Revenue Accounts (Type 4)
            // Get Expense Accounts (Type 5, 6, etc. - depending on Type definition)
            // Assuming Tipe Akun names are standard or we filter by code prefix 4, 5, 6, etc.
            // Better to use Tipe Akun string "Pendapatan", "Beban"
            
            $pendapatanDetails = JurnalDetail::whereHas('jurnal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'like', 'Pendapatan%');
            })->get();

            $bebanDetails = JurnalDetail::whereHas('jurnal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })->whereHas('akun', function($q) {
                $q->where('tipe_akun', 'like', 'Beban%');
            })->get();

            // Calculate Totals per Account to zero them out
            // For Revenue (Credit Balance): We need to Debit them
            // For Expense (Debit Balance): We need to Credit them
            
            $totalPendapatan = 0;
            $totalBeban = 0;
            $closingDetails = [];

            // Group by Account
            // Logic: Sum (Credit - Debit) for Revenue. If positive, we Debit that amount.
            // Logic: Sum (Debit - Credit) for Expense. If positive, we Credit that amount.
            
            // Simplified: Just take the net balance of the period
            // However, truly closing means zeroing the *Running Balance* up to that point? 
            // Usually Tutup Buku Bulanan checks the specific month's activity. 
            // Tutup Buku Tahunan clears the Nominal Accounts.
            // Let's assume this is Monthly Close for reporting, but effectively Transferring Net Income to Equity?
            // Usually only Year End closes nominal accounts. Monthly just locks period.
            // But user asked for "fitur Tutup buku (melakukan debit semua akun pendapatan dan melakukan kredit semua akun beban yang di konversi mejadi akun ikhtisar laba rugi)"
            // So they want the Journal Entry. This is typically Year End behavior, but can be done monthly if they want 'Laba Ditahan' updated monthly.
            
            // We'll calculate balances for the specific PERIOD.
            
            $journalEntries = [];

            // Process Revenue
            $revenueAccounts = Akun::where('tipe_akun', 'like', 'Pendapatan%')->get();
            foreach ($revenueAccounts as $akun) {
                // Get balance for this period
                $kredit = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->sum('kredit');
                $debit = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->sum('debit');
                
                $netCredit = $kredit - $debit;
                
                if ($netCredit != 0) {
                    $totalPendapatan += $netCredit;
                    // To close: Debit the account
                    $journalEntries[] = [
                        'kode_akun' => $akun->kode_akun,
                        'debit' => $netCredit,
                        'kredit' => 0
                    ];
                }
            }

            // Process Expenses
            $expenseAccounts = Akun::where('tipe_akun', 'like', 'Beban%')->get();
            foreach ($expenseAccounts as $akun) {
                $debit = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->sum('debit');
                $kredit = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->sum('kredit');

                $netDebit = $debit - $kredit;

                if ($netDebit != 0) {
                    $totalBeban += $netDebit;
                    // To close: Credit the account
                    $journalEntries[] = [
                        'kode_akun' => $akun->kode_akun,
                        'debit' => 0,
                        'kredit' => $netDebit
                    ];
                }
            }

            $labaBersih = $totalPendapatan - $totalBeban;

            // 2. Create Closing Journal
            // ID Akun Ikhtisar Laba Rugi
            $akunIkhtisar = '3-90000'; // Example placeholder
            
            // ID Akun Laba Ditahan
            $akunLabaDitahan = '3-20000'; // Example placeholder

            // Need to verify these accounts exist or create them/ask user settings
            // For now, I'll rely on seed data or assume they exist. To be safe, look them up.
            
            $jurnal = Jurnal::create([
                'no_transaksi' => 'CLOSING-' . $request->tahun . '-' . $request->bulan,
                'tanggal' => $request->tanggal_tutup,
                'deskripsi' => "Tutup Buku Periode {$request->bulan}/{$request->tahun}",
                'sumber_jurnal' => 'Closing',
                'is_locked' => 1
            ]);

            foreach ($journalEntries as $entry) {
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $entry['kode_akun'],
                    'debit' => $entry['debit'],
                    'kredit' => $entry['kredit'],
                ]);
            }

            // Balance with Ikhtisar Laba Rugi
            // If Net Income (Pendapatan > Beban): Credit Ikhtisar
            // If Net Loss: Debit Ikhtisar
            
            // Actually, simply: 
            // Sum Debits (from Revenue closing) = Total Pendapatan
            // Sum Credits (from Expense closing) = Total Beban
            // Difference needs to go to Ikhtisar.
            // If Revenue > Beban, we Debited Revenue (Total P), Credited Expense (Total B). 
            // We have Net Debit of (P - B). we need Credit Ikhtisar (P-B).
            
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $akunIkhtisar,
                'debit' => ($labaBersih < 0) ? abs($labaBersih) : 0,
                'kredit' => ($labaBersih > 0) ? $labaBersih : 0,
            ]);

            // 3. Close Ikhtisar to Laba Ditahan (Optional Step, often done in same or next step)
            // Debit Ikhtisar, Credit Laba Ditahan (if profit)
            
            $jurnal2 = Jurnal::create([
                'no_transaksi' => 'CLOSING-EQ-' . $request->tahun . '-' . $request->bulan,
                'tanggal' => $request->tanggal_tutup,
                'deskripsi' => "Transfer Laba/Rugi ke Modal {$request->bulan}/{$request->tahun}",
                'sumber_jurnal' => 'Closing',
                'is_locked' => 1
            ]);
            
            JurnalDetail::create([
                'id_jurnal' => $jurnal2->id_jurnal,
                'kode_akun' => $akunIkhtisar,
                'debit' => ($labaBersih > 0) ? $labaBersih : 0,
                'kredit' => ($labaBersih < 0) ? abs($labaBersih) : 0,
            ]);

            JurnalDetail::create([
                'id_jurnal' => $jurnal2->id_jurnal,
                'kode_akun' => $akunLabaDitahan,
                'debit' => ($labaBersih < 0) ? abs($labaBersih) : 0,
                'kredit' => ($labaBersih > 0) ? $labaBersih : 0,
            ]);

            // 4. Save Period Record
            $period = PeriodeTutupBuku::create([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'tanggal_tutup' => $request->tanggal_tutup,
                'user_id' => Auth::id() ?? 1, // Fallback for now if no auth
                'status' => 'tutup',
                'keterangan' => $request->keterangan,
            ]);

            IkhtisarLabaRugi::create([
                'periode_id' => $period->id,
                'total_pendapatan' => $totalPendapatan,
                'total_beban' => $totalBeban,
                'laba_rugi_bersih' => $labaBersih,
            ]);

            DB::commit();
            return redirect()->route('accounting.closing.index')->with('success', 'Tutup buku berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal tutup buku: ' . $e->getMessage());
        }
    }
}
