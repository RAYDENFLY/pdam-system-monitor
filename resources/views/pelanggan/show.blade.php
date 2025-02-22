@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pelanggan</h2>
    <p><strong>ID Pelanggan:</strong> {{ $pelanggan->id }}</p>
    <p><strong>Nama:</strong> {{ $pelanggan->nama }}</p>
    <p><strong>Alamat:</strong> {{ $pelanggan->alamat }}</p>
    <p><strong>No. Telepon:</strong> {{ $pelanggan->no_telepon }}</p>
    <p><strong>KWH Terakhir:</strong> {{ $pelanggan->kwh_terakhir }}</p>
    <a href="{{ route('pelanggan.index') }}" class="btn btn-primary">Kembali</a>

    <!-- Riwayat Pembayaran -->
    <h3 class="mt-4">Riwayat Pembayaran</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal Pembayaran</th>
                <th>Jumlah Bayar</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembayarans as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->tanggal_pembayaran }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{{ $pembayaran->metode_pembayaran }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada pembayaran</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
