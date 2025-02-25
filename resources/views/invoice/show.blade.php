@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-white p-4 shadow-lg rounded" style="width: 600px;">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <!-- Header -->
        <div class="text-center">
        <img src="{{ asset('images/image.png') }}" alt="Logo UPTD Pasar Cipanas" width="100" height="100">
        <h2 class="mt-2"><strong>UPTD PASAR CIPANAS</strong></h2>
            <h4 class="text-muted">PELAKSANA PENGELOLAAN LISTRIK & AIR PASAR CIPANAS</h4>
        </div>

        <!-- Title -->
        <div class="text-center bg-primary text-white py-2 my-3 rounded">
            <h5 class="mb-0">BUKTI TAGIHAN LISTRIK PASAR CIPANAS</h5>
        </div>

        <!-- Informasi Pelanggan -->
        <div class="d-flex justify-content-between">
            <div>
                <p><strong>ID PEL:</strong> {{ $pelanggan->nomor_pelanggan }}</p>
                <p><strong>NAMA:</strong> {{ $pelanggan->nama }}</p>
                <p><strong>LOKASI:</strong> {{ $pelanggan->alamat ?? '-' }}</p>
                <p><strong>TRP/DAYA:</strong> {{ $tarif_daya ?? '-' }}</p>
            </div>
            <div>
                <p><strong>REKENING BULAN:</strong> {{ now()->format('M-Y') }}</p>
                <p><strong>STAND AWAL:</strong> {{ $pelanggan->kwh_bulan_lalu }}</p>
                <p><strong>STAND AKHIR:</strong> {{ $pelanggan->kwh_terakhir }}</p>
                <p><strong>KWH PAKAI:</strong> {{ $total_pemakaian }}</p>
            </div>
        </div>

        <!-- Rincian Biaya -->
        <div class="d-flex justify-content-between font-weight-bold mt-3">
            <div>
                <p>TAGIHAN LISTRIK:</p>
                <p>ABUDEMEN:</p>
                <p>BI ADM:</p>
                <p>TOTAL:</p>
            </div>
            <div class="text-right">
                <p>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</p>
                <p>Rp {{ number_format($pembayaranTerakhir->biaya_abodemen ?? 0, 0, ',', '.') }}</p>
                <p>Rp {{ number_format($pembayaranTerakhir->biaya_admin ?? 2500, 0, ',', '.') }}</p>
                <p>Rp {{ number_format($total_tagihan + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0), 0, ',', '.') }}</p>
            </div>
        </div>



        <!-- Total Pembayaran dalam kata -->
        <div class="text-center font-italic my-3">
        // {{ ucwords(\App\Helpers\Terbilang::make($total_tagihan + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0))) }} Rupiah //
        </div>

                <!-- Tanggal Pembayaran & Status -->
                <div class="mt-3 flex justify-between items-center">
            <p><strong>Tanggal Pembayaran Terakhir:</strong> 
                {{ $pelanggan->tanggal_pembayaran_terakhir ? \Carbon\Carbon::parse($pelanggan->tanggal_pembayaran_terakhir)->format('d M Y') : '-' }}
            </p>
            
            @php
                $sudahTerbayar = $pembayaranTerakhir ? $pembayaranTerakhir->isLunas() : false;
            @endphp

            <span class="px-4 py-2 text-white text-sm rounded-md 
                {{ $sudahTerbayar ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $sudahTerbayar ? 'Lunas' : 'Belum Dibayar' }}
            </span>
        </div>
      
        <!-- Tombol Aksi -->
        <div class="no-print d-flex justify-content-between mt-4 print:hidden">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
