@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">History Pembayaran</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('pembayaran.index') }}" class="mb-4">
        <input type="text" name="search" placeholder="Cari Nomor Pelanggan / Nama" class="form-control" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nomor Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Total Pemakaian (KWH)</th>
                <th>Total per KWH (Rp)</th>
                <th>Jumlah Dibayar</th>
                <th>Denda</th>
                <th>Biaya Admin</th>
                <th>Biaya Abodemen</th>
                <th>Total Tagihan per Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKeseluruhan = 0;
            @endphp
            @foreach ($pembayarans as $pembayaran)
            @php
                $hargaPerKwh = $pembayaran->total_pemakaian > 0 ? $pembayaran->total_tagihan / $pembayaran->total_pemakaian : 0;
                $totalPerTransaksi = $pembayaran->total_tagihan + $pembayaran->denda + $pembayaran->biaya_admin + $pembayaran->biaya_abodemen;
                $totalKeseluruhan += $totalPerTransaksi;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}</td>
                <td>{{ $pembayaran->nomor_pelanggan }}</td>
                <td>{{ $pembayaran->pelanggan->nama ?? '-' }}</td>
                <td>{{ $pembayaran->total_pemakaian }} KWH</td>
                <td>Rp {{ number_format($hargaPerKwh, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->biaya_abodemen, 0, ',', '.') }}</td>
                <td><strong>Rp {{ number_format($totalPerTransaksi, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-weight-bold">
                <td colspan="9" class="text-right">Total Tagihan Keseluruhan:</td>
                <td><strong>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
