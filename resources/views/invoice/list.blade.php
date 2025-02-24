@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Invoice</h2>

         <!-- Tombol Kembali ke Dashboard -->
         <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

    <!-- Form Pencarian -->
    <form method="GET" action="{{ route('invoice.list') }}">
        <div class="input-group mb-3">
            <input type="text" name="search" class="form-control" placeholder="Cari Nomor Pelanggan atau Nama" value="{{ request('search') }}">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nomor Pelanggan</th>
                <th>Nama</th>
                <th>Total Tagihan</th>
                <th>Jumlah Dibayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
            <tr>
                <td>{{ $invoice->pelanggan->nomor_pelanggan }}</td>
                <td>{{ $invoice->pelanggan->nama }}</td>
                <td>Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($invoice->jumlah_dibayar, 0, ',', '.') }}</td>
                <td>
                    @if ($invoice->isLunas())
                        <span class="badge bg-success">Lunas</span>
                    @else
                        <span class="badge bg-danger">Belum Lunas</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('invoice.show', $invoice->pelanggan->nomor_pelanggan) }}" class="btn btn-primary btn-sm">
                        Cetak Invoice
                    </a>

                                    <!-- Jika belum lunas, tampilkan tombol Tandai Lunas -->
                @if (!$invoice->isLunas())
                    <form method="POST" action="{{ route('invoice.updateStatus', $invoice->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Tandai invoice ini sebagai lunas?')">
                            Tandai Lunas
                        </button>
                    </form>
                @endif

                <!-- Jika sudah lunas dan user adalah admin, tampilkan tombol Tandai Tidak Lunas -->
                @if ($invoice->isLunas() && auth()->user()->role == 'admin')
                    <form method="POST" action="{{ route('invoice.markUnpaid', $invoice->id) }}" style="display:inline;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Tandai invoice ini sebagai belum lunas?')">
                            Tandai Tidak Lunas
                        </button>
                    </form>
                @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
