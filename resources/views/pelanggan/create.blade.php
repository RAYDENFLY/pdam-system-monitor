@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Pelanggan</h2>

    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nomor Pelanggan</label>
            <input type="text" name="nomor_pelanggan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telepon" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Block Rumah</label>
            <input type="text" name="block_rumah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori Tarif</label>
            <select name="kategori_tarif" class="form-control" required>
                <option value="" disabled selected>Pilih Kategori Tarif</option>
                <option value="R1">R1 (450 VA)</option>
                <option value="R2">R2 (900 VA)</option>
                <option value="R3">R3 (1300 VA)</option>
                <option value="B1">B1 (2200 VA)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">KWH Terakhir</label>
            <input type="number" name="kwh_terakhir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Join</label>
            <input type="date" name="tanggal_join" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pembayaran Terakhir</label>
            <input type="date" name="tanggal_pembayaran_terakhir" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
