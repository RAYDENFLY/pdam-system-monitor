@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg p-8 border">
        <!-- Header Invoice -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Invoice Tagihan Pelanggan</h2>
            <h4 class="text-lg text-gray-600">Listrik Pasar Cipanas</h4>
        </div>

        <div class="border-b border-gray-300 mb-6"></div>

        <!-- Informasi Umum -->
        <div class="grid grid-cols-2 gap-6 text-gray-700">
            <div>
                <p><strong>Tanggal Invoice:</strong> {{ now()->format('d M Y') }}</p>
                <p><strong>Periode Tagihan:</strong> 
                    {{ \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('F Y') }} - {{ now()->format('F Y') }}
                </p>
                <p><strong>Tanggal Jatuh Tempo:</strong> 20 {{ now()->format('F Y') }}</p>
            </div>
            <div class="text-right">
                <p><strong>Tanggal Pembayaran:</strong>  
                    {{ $pelanggan->tanggal_pembayaran_terakhir ? \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('d M Y') : '-' }}
                </p>
            </div>
        </div>

        <div class="border-b border-gray-300 my-6"></div>

        <!-- Informasi Pelanggan -->
        <div class="bg-gray-100 p-5 rounded-md">
            <h3 class="text-xl font-semibold mb-3 text-gray-800">Informasi Pelanggan</h3>
            <div class="grid grid-cols-2 gap-4">
                <p><strong>Nomor Pelanggan:</strong> {{ $pelanggan->nomor_pelanggan }}</p>
                <p><strong>Nama:</strong> {{ $pelanggan->nama }}</p>
                <p class="col-span-2"><strong>Alamat:</strong> {{ $pelanggan->alamat }}</p>
            </div>
        </div>

        <div class="border-b border-gray-300 my-6"></div>

        <!-- Informasi Pemakaian Listrik -->
        <div class="bg-gray-100 p-5 rounded-md">
            <h3 class="text-xl font-semibold mb-3 text-gray-800">Informasi Pemakaian Listrik</h3>
            <div class="grid grid-cols-3 gap-4">
                <p><strong>KWH Bulan Lalu:</strong> {{ $pelanggan->kwh_bulan_lalu }}</p>
                <p><strong>KWH Terakhir:</strong> {{ $pelanggan->kwh_terakhir }}</p>
                <p><strong>Total Pemakaian:</strong> {{ $total_pemakaian }} KWH</p>
            </div>
        </div>

        <div class="border-b border-gray-300 my-6"></div>

        <!-- Rincian Tagihan -->
        <div class="bg-gray-100 p-5 rounded-md">
            <h3 class="text-xl font-semibold mb-3 text-gray-800">Rincian Tagihan</h3>
            <table class="w-full border-collapse border border-gray-300">
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-3 text-left">Deskripsi</th>
                    <th class="border border-gray-300 p-3 text-right">Jumlah</th>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Tarif Per KWH</td>
                    <td class="border border-gray-300 p-3 text-right">Rp 1.500</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Total Tagihan</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Denda</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($total_denda, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-gray-200 font-bold">
                    <td class="border border-gray-300 p-3">Total Pembayaran</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                </tr>
            </table>

            @php
                $sudahTerbayar = $pembayaranTerakhir ? $pembayaranTerakhir->isLunas() : false;
            @endphp

            <div class="mt-4">
                <span class="inline-block px-4 py-2 text-white text-sm rounded-md 
                    {{ $sudahTerbayar ? 'bg-green-500' : 'bg-red-500' }}">
                    {{ $sudahTerbayar ? 'Lunas' : 'Belum Dibayar' }}
                </span>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-between mt-6 print:hidden">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <a href="{{ (auth()->check() && in_array(auth()->user()->role, ['admin', 'teknisi', 'kasir'])) ? route('dashboard') : url('/') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md">
    <i class="fas fa-arrow-left"></i> Kembali
</a>
            </a>
        </div>
    </div>
</div>
@endsection
