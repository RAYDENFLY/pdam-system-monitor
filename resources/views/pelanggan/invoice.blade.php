@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Invoice Tagihan Pelanggan</h2>

    <div class="card p-4">
        <table class="table table-bordered">
            <tr>
                <th>Nomor Pelanggan</th>
                <td>{{ $pelanggan->nomor_pelanggan }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $pelanggan->nama }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $pelanggan->alamat }}</td>
            </tr>
            <tr>
                <th>KWH Bulan Lalu</th>
                <td>{{ $pelanggan->kwh_bulan_lalu }}</td>
            </tr>
            <tr>
                <th>KWH Terakhir</th>
                <td>{{ $pelanggan->kwh_terakhir }}</td>
            </tr>
            <tr>
                <th>Total Pemakaian</th>
                <td>{{ $total_pemakaian }} KWH</td>
            </tr>
            <tr>
                <th>Total Tagihan</th>
                <td>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Denda</th>
                <td>Rp {{ number_format($pembayaran->denda ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-{{ $sudahTerbayar ? 'success' : 'warning' }}">
                <th>Total Pembayaran</th>
                <td>
                    <strong>Rp {{ number_format($total_tagihan + ($pembayaran->denda ?? 0), 0, ',', '.') }}</strong>
                    <br>
                    <span class="badge bg-{{ $sudahTerbayar ? 'success' : 'danger' }}">
                        {{ $sudahTerbayar ? 'Lunas' : 'Belum Dibayar' }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="d-flex justify-content-between">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
