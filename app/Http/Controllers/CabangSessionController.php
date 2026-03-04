<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CabangSessionController extends Controller
{
    /**
     * Switch active cabang/unit for admin/superuser.
     * Stored in session for CabangScope filtering.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'active_cabang' => 'nullable|integer',
            'active_unit' => 'nullable|integer',
        ]);

        if ($request->has('active_cabang')) {
            if ($request->active_cabang) {
                session(['active_cabang' => (int) $request->active_cabang]);
            } else {
                session()->forget('active_cabang');
            }
            // Clear unit when switching cabang
            session()->forget('active_unit');
        }

        if ($request->has('active_unit')) {
            if ($request->active_unit) {
                session(['active_unit' => (int) $request->active_unit]);
            } else {
                session()->forget('active_unit');
            }
        }

        return back()->with('success', 'Filter cabang/unit berhasil diubah.');
    }
}
