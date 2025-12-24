<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\ApprovalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Display inbox of pending approvals.
     */
    public function inbox()
    {
        $user = auth()->user();
        
        // Get pending pinjaman approvals
        $pendingPinjaman = Pinjaman::with(['anggota', 'jenisPinjaman'])
            ->where('status', 'pending_approval')
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        return view('approval.inbox', compact('pendingPinjaman'));
    }

    /**
     * Approve a request.
     */
    public function approve(Request $request, string $module, string $id)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($module === 'pinjaman') {
                $pinjaman = Pinjaman::findOrFail($id);

                if ($pinjaman->status !== 'pending_approval') {
                    return back()->with('error', 'Pinjaman tidak dalam status pending approval.');
                }

                // Record approval
                ApprovalHistory::create([
                    'module' => 'pinjaman',
                    'reference_id' => $pinjaman->id_pinjaman,
                    'level' => 1,
                    'user_id' => auth()->id(),
                    'action' => 'approve',
                    'notes' => $validated['notes'] ?? 'Disetujui',
                ]);

                // Update status
                $pinjaman->update([
                    'status' => 'approved',
                    'tanggal_persetujuan' => now(),
                ]);

                DB::commit();

                return redirect()->route('approval.inbox')
                    ->with('success', 'Pinjaman ' . $pinjaman->no_pinjaman . ' berhasil disetujui.');
            }

            return back()->with('error', 'Module tidak dikenali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Reject a request.
     */
    public function reject(Request $request, string $module, string $id)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($module === 'pinjaman') {
                $pinjaman = Pinjaman::findOrFail($id);

                if ($pinjaman->status !== 'pending_approval') {
                    return back()->with('error', 'Pinjaman tidak dalam status pending approval.');
                }

                // Record rejection
                ApprovalHistory::create([
                    'module' => 'pinjaman',
                    'reference_id' => $pinjaman->id_pinjaman,
                    'level' => 1,
                    'user_id' => auth()->id(),
                    'action' => 'reject',
                    'notes' => $validated['notes'],
                ]);

                // Update status
                $pinjaman->update([
                    'status' => 'rejected',
                ]);

                DB::commit();

                return redirect()->route('approval.inbox')
                    ->with('success', 'Pinjaman ' . $pinjaman->no_pinjaman . ' telah ditolak.');
            }

            return back()->with('error', 'Module tidak dikenali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }
}
