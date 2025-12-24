@extends('laporan.pdf.layout')

@section('content')
<table>
    <!-- ASET Section -->
    <tr class="section-header">
        <td colspan="2">ASET</td>
    </tr>
    
    <!-- Aset Lancar -->
    <tr class="sub-section">
        <td colspan="2">Aset Lancar</td>
    </tr>
    @foreach($aktivaLancar as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Aset Lancar</td>
        <td class="amount">Rp {{ number_format($totalAktivaLancar, 0, ',', '.') }}</td>
    </tr>

    <!-- Aset Tetap -->
    <tr class="sub-section">
        <td colspan="2">Aset Tetap</td>
    </tr>
    @foreach($aktivaTetap as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Aset Tetap</td>
        <td class="amount">Rp {{ number_format($totalAktivaTetap, 0, ',', '.') }}</td>
    </tr>

    <tr class="grand-total">
        <td>TOTAL ASET</td>
        <td class="amount">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</td>
    </tr>

    <!-- Empty row for spacing -->
    <tr><td colspan="2" style="height: 20px; border: none;"></td></tr>

    <!-- LIABILITAS Section -->
    <tr class="section-header">
        <td colspan="2">LIABILITAS</td>
    </tr>

    <!-- Liabilitas -->
    <tr class="sub-section">
        <td colspan="2">Liabilitas Jangka Pendek</td>
    </tr>
    @foreach($kewajiban as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Liabilitas</td>
        <td class="amount">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
    </tr>

    <!-- Empty row -->
    <tr><td colspan="2" style="height: 10px; border: none;"></td></tr>

    <!-- EKUITAS Section -->
    <tr class="section-header">
        <td colspan="2">EKUITAS</td>
    </tr>
    @foreach($modal as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr>
        <td class="indent-1">Laba Bersih Periode Berjalan</td>
        <td class="amount">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
    </tr>
    <tr class="total-row">
        <td>Total Ekuitas</td>
        <td class="amount">Rp {{ number_format($totalModal + $labaBersih, 0, ',', '.') }}</td>
    </tr>

    <!-- Empty row -->
    <tr><td colspan="2" style="height: 10px; border: none;"></td></tr>

    <tr class="grand-total">
        <td>TOTAL LIABILITAS DAN EKUITAS</td>
        <td class="amount">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</td>
    </tr>
</table>

@if($totalAktiva != $totalPasiva)
<p style="color: red; text-align: center; margin-top: 10px;">
    <strong>PERHATIAN: Neraca tidak balance! Selisih: Rp {{ number_format(abs($totalAktiva - $totalPasiva), 0, ',', '.') }}</strong>
</p>
@endif
@endsection
