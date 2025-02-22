@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Pelanggan</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tombol Kembali ke Dashboard -->
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary mb-3">Tambah Pelanggan</a>

    <!-- Form Filter -->
    <form action="{{ route('pelanggan.index') }}" method="GET" class="mb-3">
        <div class="row">
            <!-- Filter Nama -->
            <div class="col-md-6">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama" class="form-control" value="{{ request('nama') }}">
            </div>

            <!-- Filter Kategori Tarif -->
            <div class="col-md-6">
                <label class="form-label">Kategori Tarif</label>
                <select name="kategori_tarif" class="form-control">
                    <option value="">Semua</option>
                    <option value="R1" {{ request('kategori_tarif') == 'R1' ? 'selected' : '' }}>R1 (450 VA)</option>
                    <option value="R2" {{ request('kategori_tarif') == 'R2' ? 'selected' : '' }}>R2 (900 VA)</option>
                    <option value="R3" {{ request('kategori_tarif') == 'R3' ? 'selected' : '' }}>R3 (1300 VA)</option>
                    <option value="B1" {{ request('kategori_tarif') == 'B1' ? 'selected' : '' }}>B1 (2200 VA)</option>
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Filter</button>
            <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Tabel Pelanggan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nomor Pelanggan</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>No. Telepon</th>
                <th>Tanggal Join</th>
                <th>Kategori Tarif</th>
                <th>KWH Terakhir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pelanggans as $pelanggan)
            <tr>
                <td>
                    <a href="{{ route('pelanggan.show', $pelanggan->nomor_pelanggan) }}">{{ $pelanggan->nomor_pelanggan }}</a>
                </td>
                <td>{{ $pelanggan->nama }}</td>
                <td>{{ $pelanggan->alamat }}</td>
                <td>{{ $pelanggan->no_telepon }}</td>
                <td>{{ $pelanggan->tanggal_join }}</td>
                <td>{{ $pelanggan->kategori_tarif }}</td>
                <td>{{ $pelanggan->kwh_terakhir }}</td>
                <td>
                    <a href="{{ route('pembayaran.history', $pelanggan->nomor_pelanggan) }}" class="btn btn-info btn-sm">History</a>
                    <a href="{{ route('pelanggan.invoice', $pelanggan->nomor_pelanggan) }}" class="btn btn-info btn-sm">Cetak Invoice</a>
                    <a href="{{ route('pelanggan.edit', $pelanggan->nomor_pelanggan) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('pelanggan.destroy', $pelanggan->nomor_pelanggan) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pelanggan ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
