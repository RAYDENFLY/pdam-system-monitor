@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard</h2>
    <div class="d-flex align-items-center">
            <strong class="me-3">👤 {{ Auth::user()->name }}</strong>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
        <br>
    <!-- Tombol Navigasi -->
     
    <div class="row mb-4">
    <div class="col-md-4">
            <a href="{{ route('admin.register') }}" class="btn btn-primary btn-lg w-100 mb-2">
                <i class="fas fa-shield"></i> Admin
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('pelanggan.index') }}" class="btn btn-primary btn-lg w-100 mb-2">
                <i class="fas fa-users"></i> Kelola Pelanggan
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('pembayaran.index') }}" class="btn btn-success btn-lg w-100 mb-2">
                <i class="fas fa-cash-register"></i> Transaksi
            </a>
        </div>
        <div class="col-md-4 text-white">
            <a href="{{ route('laporan.index') }}" class="btn btn-warning btn-lg w-100 mb-2">
                <i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan
            </a>
        </div>
                
                <!-- Tombol Invoice -->
        <div class="col-md-4">
            <button class="btn btn-secondary btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                <i class="fas fa-file-invoice"></i> Invoice
            </button>
        </div>

        

        <!-- Pop-up Modal -->
        <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="invoiceModalLabel">Pilihan Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <a href="{{ route('invoice.list') }}" class="btn btn-primary btn-lg w-100 mb-2">
                     <i class="fas fa-file-invoice"></i> List Invoice
                     </a>
                        <a href="{{ route('invoice.create') }}" class="btn btn-success w-100 mb-2">Buat Invoice</a>
                    </div>
                </div>
            </div>
        </div>



    <!-- Statistik -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Pelanggan</h5>
                    <p class="card-text fs-2">{{ $jumlahPelanggan }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Tagihan Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pengeluaran Bulan Ini</h5>
                    <p class="card-text fs-2">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pelanggan Belum Bayar</h5>
                    <p class="card-text fs-2">{{ $jumlahBelumBayar }}</p>
                </div>
            </div>
        </div>
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
