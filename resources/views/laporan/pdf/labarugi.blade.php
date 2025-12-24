@extends('laporan.pdf.layout')

@section('content')
<table>
    <!-- PENDAPATAN Section -->
    <tr class="section-header">
        <td colspan="2">PENDAPATAN</td>
    </tr>
    @foreach($pendapatan as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Pendapatan</td>
        <td class="amount">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
    </tr>

    <!-- HPP Section -->
    <tr class="section-header">
        <td colspan="2">HARGA POKOK PENJUALAN</td>
    </tr>
    @foreach($hpp as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total HPP</td>
        <td class="amount">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
    </tr>

    <tr class="grand-total">
        <td>LABA KOTOR</td>
        <td class="amount {{ $labaKotor >= 0 ? 'positive' : 'negative' }}">
            Rp {{ number_format($labaKotor, 0, ',', '.') }}
        </td>
    </tr>

    <!-- Empty row -->
    <tr><td colspan="2" style="height: 15px; border: none;"></td></tr>

    <!-- BEBAN OPERASIONAL Section -->
    <tr class="section-header">
        <td colspan="2">BEBAN OPERASIONAL</td>
    </tr>
    @foreach($beban as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Beban Operasional</td>
        <td class="amount">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
    </tr>

    <tr class="grand-total">
        <td>LABA OPERASIONAL</td>
        <td class="amount {{ $labaOperasional >= 0 ? 'positive' : 'negative' }}">
            Rp {{ number_format($labaOperasional, 0, ',', '.') }}
        </td>
    </tr>

    <!-- Empty row -->
    <tr><td colspan="2" style="height: 15px; border: none;"></td></tr>

    <!-- PENDAPATAN/BEBAN LAIN Section -->
    <tr class="section-header">
        <td colspan="2">PENDAPATAN / BEBAN LAIN-LAIN</td>
    </tr>
    @foreach($pendapatanLain as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount">Rp {{ number_format($item['saldo'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
    @foreach($bebanLain as $item)
    <tr>
        <td class="indent-1">{{ $item['nama'] }}</td>
        <td class="amount negative">( Rp {{ number_format(abs($item['saldo']), 0, ',', '.') }} )</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>Total Pendapatan/Beban Lain</td>
        <td class="amount">Rp {{ number_format($totalPendapatanLain - $totalBebanLain, 0, ',', '.') }}</td>
    </tr>

    <!-- Empty row -->
    <tr><td colspan="2" style="height: 15px; border: none;"></td></tr>

    <tr class="grand-total" style="font-size: 12px;">
        <td>LABA BERSIH</td>
        <td class="amount {{ $labaBersih >= 0 ? 'positive' : 'negative' }}" style="font-size: 12px;">
            Rp {{ number_format($labaBersih, 0, ',', '.') }}
        </td>
    </tr>
</table>
@endsection
