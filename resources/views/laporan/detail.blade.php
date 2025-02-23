@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Detail Laporan Keuangan</h2>

    <div class="card mb-3">
        <div class="card-body">
            <h4>Pelanggan: {{ $pelanggan->nama }}</h4>
            <p>Nomor Pelanggan: {{ $pelanggan->nomor_pelanggan }}</p>
            <p>Kategori Tarif: {{ $pelanggan->kategori_tarif }}</p>
            <p>Total Pemakaian: {{ number_format($total_pemakaian, 0, ',', '.') }} KWH</p>
            
            <p>Total Tagihan: <strong>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</strong></p>

            <p>Total Denda: 
                <strong>Rp {{ number_format($total_denda, 0, ',', '.') }}</strong>
            </p>

            <p><strong>Total yang Harus Dibayar: Rp {{ number_format($total_tagihan + $total_denda, 0, ',', '.') }}</strong></p>

            <!-- Status Pembayaran -->
            <p class="font-bold text-lg">
                <strong>Status Pembayaran:</strong>
                <span class="badge bg-{{ $sudahTerbayar ? 'success' : 'danger' }}">
                    {{ $sudahTerbayar ? 'Lunas' : 'Belum Dibayar' }}
                </span>
            </p>

            <!-- Total Pembayaran -->
            <p class="font-bold text-lg">
                <strong>Total Pembayaran:</strong> Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
            </p>

        </div>
    </div>

    <h4>Riwayat Pembayaran</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Tagihan</th>
                <th>Denda</th>
                <th>Jumlah Dibayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pelanggan->pembayarans as $pembayaran)
            <tr>
            <td>{{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-m-Y') : '-' }}</td>
            <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                <td>
                    <span class="badge bg-{{ $pembayaran->jumlah_dibayar >= ($pembayaran->total_tagihan + $pembayaran->denda) ? 'success' : 'danger' }}">
                        {{ $pembayaran->jumlah_dibayar >= ($pembayaran->total_tagihan + $pembayaran->denda) ? 'Lunas' : 'Belum Dibayar' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('laporan.exportCSV', $pelanggan->nomor_pelanggan) }}" class="btn btn-primary">
        Export ke CSV
    </a>

    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
