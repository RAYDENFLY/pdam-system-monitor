@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Laporan Keuangan</h2>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('laporan.index') }}" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Total Pemasukan -->
    <div class="alert alert-info mb-4">
        <strong>Total Pemasukan: </strong> Rp {{ number_format($total_pemasukan, 0, ',', '.') }}
    </div>

    <!-- Tabel Laporan Keuangan -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
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
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('laporan.show', $pembayaran->nomor_pelanggan) }}" class="text-primary font-weight-bold">
                            {{ $pembayaran->nomor_pelanggan }}
                        </a>
                    </td>
                    <td>{{ number_format($pembayaran->total_pemakaian, 0, ',', '.') }} KWH</td>
                    <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tombol Kembali dan Export -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-house-door"></i> Kembali ke Dashboard
        </a>


@endsection
