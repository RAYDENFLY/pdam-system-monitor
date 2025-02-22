@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">History Pembayaran</h2>

    <table class="table table-bordered">
    <thead>
    <tr>
        <th>Tanggal</th>
        <th>Nomor Pelanggan</th>
        <th>Total Pemakaian</th>
        <th>Total Tagihan</th>
        <th>Jumlah Dibayar</th>
        <th>Denda</th> <!-- Tambahkan kolom denda -->
    </tr>
</thead>
<tbody>
    @foreach ($pembayarans as $pembayaran)
    <tr>
        <td>{{ $pembayaran->tanggal_pembayaran }}</td>
        <td>{{ $pembayaran->nomor_pelanggan }}</td>
        <td>{{ $pembayaran->total_pemakaian }} KWH</td>
        <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
        <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
        <td>Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</td> <!-- Tampilkan denda -->
    </tr>
    @endforeach
</tbody>


    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
