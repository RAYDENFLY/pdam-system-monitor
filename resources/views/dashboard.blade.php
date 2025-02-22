@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row">
        <!-- Jumlah Pelanggan -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Pelanggan</h5>
                    <p class="card-text fs-2">{{ $jumlahPelanggan }}</p>
                </div>
            </div>
        </div>

        <!-- Total Tagihan Bulan Ini -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Tagihan Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pengeluaran Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Jumlah yang Belum Bayar -->
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pelanggan Belum Bayar</h5>
                    <p class="card-text fs-2">{{ $jumlahBelumBayar }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Belum Terbayar -->
        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Invoice Belum Terbayar</h5>
                    <p class="card-text fs-2">{{ $jumlahInvoiceBelumTerbayar }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
