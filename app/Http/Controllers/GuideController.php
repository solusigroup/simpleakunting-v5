<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class GuideController extends Controller
{
    public function index()
    {
        return view('guide.index');
    }

    public function downloadPdf()
    {
        $perusahaan = DB::table('perusahaan')->first();
        
        $data = [
            'perusahaan' => $perusahaan,
            'tanggal' => date('d F Y'),
        ];

        $pdf = Pdf::loadView('guide.pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Panduan_SimpleAkunting_v5.pdf');
    }
}
