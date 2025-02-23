@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-center">Invoice Tagihan Pelanggan</h2>
        <h4 class="text-lg text-center text-gray-600">Listrik Pasar Cipanas</h4>

        <div class="border-b border-gray-300 my-4"></div>

        <!-- Informasi Umum Invoice -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-700"><strong>Tanggal Invoice:</strong> {{ now()->format('d M Y') }}</p>
                <p class="text-gray-700"><strong>Periode Tagihan:</strong> 
                    {{ \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('F Y') }} - {{ now()->format('F Y') }}
                </p>
                <p class="text-gray-700"><strong>Tanggal Jatuh Tempo:</strong> 20 {{ now()->format('F Y') }}</p>
            </div>
            <div>
                <p class="text-gray-700"><strong>Tanggal Pembayaran:</strong> {{ $pembayaran->tanggal_pembayaran ?? '-' }}</p>
                <p class="text-gray-700"><strong>Metode Pembayaran:</strong> {{ $pembayaran->metode_pembayaran ?? '-' }}</p>
            </div>
        </div>

        <div class="border-b border-gray-300 my-4"></div>

        <!-- Informasi Pelanggan -->
        <div class="bg-gray-100 p-4 rounded-md">
            <h3 class="text-lg font-semibold mb-2">Informasi Pelanggan</h3>
            <p><strong>Nomor Pelanggan:</strong> {{ $pelanggan->nomor_pelanggan }}</p>
            <p><strong>Nama:</strong> {{ $pelanggan->nama }}</p>
            <p><strong>Alamat:</strong> {{ $pelanggan->alamat }}</p>
            <p><strong>Pembayaran Terakhir:</strong> 
                {{ $pelanggan->tanggal_pembayaran_terakhir ? \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('d M Y') : '-' }}
            </p>
        </div>

        <div class="border-b border-gray-300 my-4"></div>

        <!-- Informasi Pemakaian Listrik -->
        <div class="bg-gray-100 p-4 rounded-md">
            <h3 class="text-lg font-semibold mb-2">Informasi Pemakaian Listrik</h3>
            <p><strong>KWH Bulan Lalu:</strong> {{ $pelanggan->kwh_bulan_lalu }}</p>
            <p><strong>KWH Terakhir:</strong> {{ $pelanggan->kwh_terakhir }}</p>
            <p><strong>Total Pemakaian:</strong> {{ $total_pemakaian }} KWH</p>
        </div>

        <div class="border-b border-gray-300 my-4"></div>
<!-- Rincian Tagihan -->
<div class="bg-gray-100 p-4 rounded-md">
    <h3 class="text-lg font-semibold mb-2">Rincian Tagihan</h3>
    <p><strong>Tarif Per KWH:</strong> Rp 1.500</p>
    <p><strong>Total Tagihan:</strong> Rp {{ number_format($total_tagihan, 0, ',', '.') }}</p>
    <p><strong>Denda:</strong> Rp {{ number_format($total_denda, 0, ',', '.') }}</p>
            
    @php
        $sudahTerbayar = $pembayaranTerakhir ? $pembayaranTerakhir->isLunas() : false;
    @endphp

    <p class="font-bold text-lg">
        <strong>Total Pembayaran:</strong> Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
    </p>

    <div class="mt-2">
        <span class="inline-block px-3 py-1 text-white text-sm rounded-md {{ $sudahTerbayar ? 'bg-green-500' : 'bg-red-500' }}">
            {{ $sudahTerbayar ? 'Lunas' : 'Belum Dibayar' }}
        </span>
    </div>
</div>

        <!-- Tombol Aksi -->
        <div class="flex justify-between mt-6">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
