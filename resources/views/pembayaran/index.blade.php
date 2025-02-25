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
                <th>Total Tagihan</th>
                <th>Jumlah Dibayar</th>
                <th>Denda</th>
                <th>Biaya Admin</th>
                <th>Biaya Abodemen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembayarans as $pembayaran)
            <tr>
                <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}</td>
                <td>{{ $pembayaran->nomor_pelanggan }}</td>
                <td>{{ $pembayaran->pelanggan->nama ?? '-' }}</td>
                <td>{{ $pembayaran->total_pemakaian }} KWH</td>
                <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->biaya_abodemen, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
