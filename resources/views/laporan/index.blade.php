@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Laporan Keuangan</h2>

    <form method="GET" action="{{ route('laporan.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <h4>Total Pemasukan: Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nomor Pelanggan</th>
                <th>Total Pemakaian</th>
                <th>Total Tagihan</th>
                <th>Jumlah Dibayar</th>
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
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
