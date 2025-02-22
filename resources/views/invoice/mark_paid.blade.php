@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tandai Lunas</h2>

    <!-- Form Pencarian -->
    <form method="GET" action="{{ route('invoice.markPaid') }}">
        <div class="mb-3">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau nomor pelanggan">
        </div>
        <button type="submit" class="btn btn-primary mb-3">Cari</button>
    </form>

    <!-- Filter Status -->
    <form method="GET" action="{{ route('invoice.markPaid') }}">
        <select name="status" class="form-select mb-3">
            <option value="">Semua</option>
            <option value="lunas">Lunas</option>
            <option value="belum">Belum Lunas</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nomor Pelanggan</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pelanggans as $pelanggan)
            <tr>
                <td>{{ $pelanggan->nomor_pelanggan }}</td>
                <td>{{ $pelanggan->nama }}</td>
                <td>
                @if ($pembayaran && $pembayaran->jumlah_dibayar >= $total_tagihan)
                    <span class="badge bg-success">Lunas</span>
                @else
                    <span class="badge bg-danger">Belum Dibayar</span>
                @endif
            </td>
                <td>
                <form method="POST" action="{{ route('invoice.markPaid.update', $pelanggan->nomor_pelanggan) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">Tandai Lunas</button>
                </form>
            </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
