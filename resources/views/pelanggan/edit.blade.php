@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Pelanggan</h2>

    <form action="{{ route('pelanggan.update', $pelanggan->nomor_pelanggan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $pelanggan->nama }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor Pelanggan</label>
            <input type="text" name="nomor_pelanggan" class="form-control" value="{{ $pelanggan->nomor_pelanggan }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control" value="{{ $pelanggan->alamat }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telepon" class="form-control" value="{{ $pelanggan->no_telepon }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Block Rumah</label>
            <input type="text" name="block_rumah" class="form-control" value="{{ $pelanggan->block_rumah }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori Tarif</label>
            <select name="kategori_tarif" class="form-control" required>
                <option value="R1" {{ $pelanggan->kategori_tarif == 'R1' ? 'selected' : '' }}>R1 (450 VA)</option>
                <option value="R2" {{ $pelanggan->kategori_tarif == 'R2' ? 'selected' : '' }}>R2 (900 VA)</option>
                <option value="R3" {{ $pelanggan->kategori_tarif == 'R3' ? 'selected' : '' }}>R3 (1300 VA)</option>
                <option value="B1" {{ $pelanggan->kategori_tarif == 'B1' ? 'selected' : '' }}>B1 (2200 VA)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Join</label>
            <input type="date" name="tanggal_join" class="form-control" value="{{ $pelanggan->tanggal_join }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pembayaran Terakhir</label>
            <input type="date" name="tanggal_pembayaran_terakhir" class="form-control" value="{{ $pelanggan->tanggal_pembayaran_terakhir }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">KWH Bulan Lalu</label>
            <input type="number" name="kwh_bulan_lalu" class="form-control" value="{{ $pelanggan->kwh_bulan_lalu }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">KWH Terakhir</label>
            <input type="number" name="kwh_terakhir" class="form-control" value="{{ $pelanggan->kwh_terakhir }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
