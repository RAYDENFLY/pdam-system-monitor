@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Invoice Tagihan Pelanggan</h2>

    <div class="card p-4">
        <!-- Informasi Umum Invoice -->
        <table class="table table-bordered">
            <tr>
                <th>Tanggal Invoice</th>
                <td>{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Periode Tagihan</th>
                <td>{{ \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('F Y') }} - {{ now()->format('F Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Jatuh Tempo</th>
                <td>20 {{ now()->format('F Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Pembayaran</th>
                <td>{{ $pembayaran->tanggal_pembayaran ?? '-' }}</td>
            </tr>
            <tr>
                <th>Metode Pembayaran</th>
                <td>{{ $pembayaran->metode_pembayaran ?? '-' }}</td>
            </tr>
        </table>

        <!-- Informasi Pelanggan -->
        <table class="table table-bordered mt-3">
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
        </table>

        <!-- Informasi Pemakaian Listrik -->
        <table class="table table-bordered mt-3">
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
        </table>

        <!-- Rincian Tagihan -->
        <table class="table table-bordered mt-3">
            <tr>
                <th>Tarif Per KWH</th>
                <td>Rp 1.500</td>
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

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-between mt-4">
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
