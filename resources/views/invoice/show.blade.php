@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg p-8 border">
        <!-- Header dengan Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('path-to-logo.png') }}" alt="Logo" class="mx-auto h-16 mb-2">
            <h2 class="text-2xl font-bold text-gray-800">UPTD PASAR CIPANAS</h2>
            <h4 class="text-md text-gray-600">PELAKSANA PENGELOLAAN LISTRIK & AIR PASAR CIPANAS</h4>
            <div class="mt-3 bg-blue-200 py-2 font-bold text-gray-800">BUKTI TAGIHAN LISTRIK PASAR CIPANAS</div>
        </div>

        <div class="border-b border-gray-300 mb-4"></div>

        <!-- Informasi Pelanggan -->
        <div class="grid grid-cols-2 gap-4 text-gray-700">
            <div>
                <p><strong>ID PEL:</strong> {{ $pelanggan->nomor_pelanggan }}</p>
                <p><strong>NAMA:</strong> {{ $pelanggan->nama }}</p>
                <p><strong>LOKASI:</strong> {{ $pelanggan->alamat }}</p>
                <p><strong>TRP/DAYA:</strong> B3-450</p>
            </div>
            <div class="text-right">
                <p><strong>REKENING BULAN:</strong> {{ now()->format('M-Y') }}</p>
                <p><strong>STAND AWAL:</strong> {{ $pelanggan->kwh_bulan_lalu }}</p>
                <p><strong>STAND AKHIR:</strong> {{ $pelanggan->kwh_terakhir }}</p>
                <p><strong>KWH PAKAI:</strong> {{ $total_pemakaian }}</p>
            </div>
        </div>

        <div class="border-b border-gray-300 my-4"></div>

        <!-- Rincian Tagihan -->
        <div class="p-4 bg-gray-100 rounded-md">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Rincian Tagihan</h3>
            <table class="w-full border-collapse border border-gray-300">
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-3 text-left">Deskripsi</th>
                    <th class="border border-gray-300 p-3 text-right">Jumlah</th>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Tagihan Listrik</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Abodemen</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($biaya_langganan, 0, ',', '.') }}                    </td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Biaya Admin</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($biaya_admin, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 p-3">Denda</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($total_denda, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-gray-200 font-bold">
                    <td class="border border-gray-300 p-3">TOTAL</td>
                    <td class="border border-gray-300 p-3 text-right">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                </tr>
            </table>
            <p class="text-center mt-3 italic">// {{ terbilang($totalPembayaran) }} Rupiah //</p>
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

        <!-- Tombol Cetak -->
        <div class="flex justify-center mt-6 print:hidden">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
        </div>
    </div>
</div>
@endsection