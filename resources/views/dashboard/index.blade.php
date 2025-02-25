@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h2 class="text-3xl font-bold mb-6">Dashboard</h2>

    <div class="flex justify-between items-center mb-6">
        <div class="text-lg font-semibold">👤 {{ Auth::user()->name }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                Logout
            </button>
        </form>
    </div>

    <!-- Tombol Navigasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @if(Auth::user()->role == 'admin')
        <a href="{{ route('admin.register') }}" class="bg-blue-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-blue-600 transition">
            <i class="fas fa-shield-alt text-2xl mr-2"></i> Admin
        </a>

        <a href="{{ route('pelanggan.index') }}" class="bg-blue-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-blue-600 transition">
            <i class="fas fa-users text-2xl mr-2"></i> Kelola Pelanggan
        </a>
        @endif

        @if(in_array(Auth::user()->role, ['admin', 'kasir']))
        <a href="{{ route('pembayaran.index') }}" class="bg-green-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-green-600 transition">
            <i class="fas fa-cash-register text-2xl mr-2"></i> Transaksi
        </a>

        <a href="{{ route('pengeluaran.index') }}" class="bg-green-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-green-600 transition">
       <i class="fas fa-money-bill-wave text-2xl mr-2"></i> Tambah Pengeluaran
       </a>


        <a href="{{ route('laporan.index') }}" class="bg-yellow-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-yellow-600 transition">
            <i class="fas fa-file-invoice-dollar text-2xl mr-2"></i> Laporan Keuangan
        </a>

        <button class="bg-gray-700 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-gray-800 transition" data-bs-toggle="modal" data-bs-target="#invoiceModal">
            <i class="fas fa-file-invoice text-2xl mr-2"></i> Invoice
        </button>

        @endif

        <!-- ✅ Tombol Tambah & Data Pelanggan -->
        <a href="{{ route('user.pelanggan') }}" class="bg-purple-500 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-purple-600 transition">
            <i class="fas fa-user-plus text-2xl mr-2"></i> Tambah & Data Pelanggan
        </a>

        <!-- ✅ Tombol Konfigurasi Web (Hanya untuk Admin) -->
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('konfigurasi.index') }}" class="bg-red-600 text-white flex items-center justify-center py-3 rounded-lg shadow-lg hover:bg-red-700 transition">
                <i class="fas fa-cogs text-2xl mr-2"></i> Konfigurasi Web
            </a>
        @endif
    </div>

    <!-- Modal Invoice -->
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
                    <a href="{{ route('invoice.create') }}" class="btn btn-success btn-lg w-100 mb-2">
                        <i class="fas fa-plus"></i> Buat Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Statistik</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Jumlah Pelanggan</h5>
                <p class="text-3xl font-bold">{{ $jumlahPelanggan }}</p>
            </div>
            <i class="fas fa-users text-4xl"></i>
        </div>

        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Total Tagihan Bulan Ini</h5>
                <p class="text-3xl font-bold">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-file-invoice-dollar text-4xl"></i>
        </div>

        <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Pendapatan Bulan Ini</h5>
                <p class="text-3xl font-bold">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-wallet text-4xl"></i>
        </div>

        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Pengeluaran Bulan Ini</h5>
                <p class="text-3xl font-bold">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-money-bill-wave text-4xl"></i>
        </div>

        <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Pelanggan Belum Bayar</h5>
                <p class="text-3xl font-bold">{{ $jumlahBelumBayar }}</p>
            </div>
            <i class="fas fa-user-times text-4xl"></i>
        </div>

        <div class="bg-gray-700 text-white p-6 rounded-lg shadow-lg flex justify-between items-center">
            <div>
                <h5 class="text-lg font-semibold">Invoice Belum Terbayar</h5>
                <p class="text-3xl font-bold">{{ $jumlahInvoiceBelumTerbayar }}</p>
            </div>
            <i class="fas fa-receipt text-4xl"></i>
        </div>
    </div>

</div>
@endsection
