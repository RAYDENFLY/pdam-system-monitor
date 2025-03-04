@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Pelanggan</h2>

    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nomor Pelanggan</label>
            <input type="text" name="nomor_pelanggan" class="form-control" required>
            <small class="text-muted">Masukkan Nomor Pelanggan atau ID unik pelanggan.<br>
            <strong>Contoh:</strong> 1234567 atau PEL001.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
            <small class="text-muted">Masukkan nama lengkap sesuai identitas resmi.<br>
            <strong>Contoh:</strong> Budi Santoso.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control" required>
            <small class="text-muted">Masukkan alamat lengkap pelanggan.<br>
            <strong>Contoh:</strong> Jl. Merdeka No.10, Jakarta.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="no_telepon" class="form-control" required>
            <small class="text-muted">Masukkan nomor telepon aktif yang bisa dihubungi.<br>
            <strong>Contoh:</strong> 081234567890.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Block Rumah</label>
            <input type="text" name="block_rumah" class="form-control" required>
            <small class="text-muted">Masukkan blok rumah pelanggan (jika ada).<br>
            <strong>Contoh:</strong> Blok A2 No.15.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori Tarif</label>
            <select name="kategori_tarif" class="form-control" required>
                <option value="" disabled selected>Pilih Kategori Tarif</option>
                <option value="R1">R1 (450 VA) - Rumah Tangga Kecil</option>
                <option value="R2">R2 (900 VA) - Rumah Tangga Menengah</option>
                <option value="R3">R3 (1300 VA) - Rumah Tangga Besar</option>
                <option value="B1">B1 (2200 VA) - Bisnis Kecil</option>
            </select>
            <small class="text-muted">Pilih tarif listrik pelanggan sesuai daya listrik terpasang.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">KWH Terakhir</label>
            <input type="number" name="kwh_terakhir" class="form-control" required>
            <small class="text-muted">Masukkan angka KWH terakhir sebelum pelanggan bergabung.<br>
            <strong>Contoh:</strong> 250.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Bergabung</label>
            <input type="date" name="tanggal_join" class="form-control" required>
            <small class="text-muted">Masukkan tanggal pelanggan pertama kali bergabung.<br>
            <strong>Contoh:</strong> 2024-01-15.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pembayaran Terakhir</label>
            <input type="date" name="tanggal_pembayaran_terakhir" class="form-control">
            <small class="text-muted">Masukkan tanggal pembayaran terakhir pelanggan (opsional).<br>
            <strong>Contoh:</strong> 2024-02-01.</small>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
