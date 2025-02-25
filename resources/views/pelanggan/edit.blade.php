@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Edit Pelanggan</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pelanggan.update', $pelanggan->nomor_pelanggan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="nama" class="form-control" value="{{ $pelanggan->nama }}" required>
                            <small class="text-muted">Masukkan nama lengkap sesuai identitas resmi.<br><strong>Contoh:</strong> Budi Santoso</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Pelanggan</label>
                            <input type="text" name="nomor_pelanggan" class="form-control" value="{{ $pelanggan->nomor_pelanggan }}" readonly>
                            <small class="text-muted">Nomor pelanggan tidak dapat diubah.<br><strong>Contoh:</strong> 123456789</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="{{ $pelanggan->alamat }}" required>
                            <small class="text-muted">Masukkan alamat lengkap sesuai domisili.<br><strong>Contoh:</strong> Jl. Merdeka No. 10, Jakarta</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ $pelanggan->no_telepon }}" required>
                            <small class="text-muted">Masukkan nomor telepon yang dapat dihubungi.<br><strong>Contoh:</strong> 081234567890</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Block Rumah</label>
                            <input type="text" name="block_rumah" class="form-control" value="{{ $pelanggan->block_rumah }}" required>
                            <small class="text-muted">Masukkan blok rumah sesuai dengan alamat.<br><strong>Contoh:</strong> H10 No 8</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Tarif</label>
                            <select name="kategori_tarif" class="form-select" required>
                                <option value="R1" {{ $pelanggan->kategori_tarif == 'R1' ? 'selected' : '' }}>R1 (450 VA)</option>
                                <option value="R2" {{ $pelanggan->kategori_tarif == 'R2' ? 'selected' : '' }}>R2 (900 VA)</option>
                                <option value="R3" {{ $pelanggan->kategori_tarif == 'R3' ? 'selected' : '' }}>R3 (1300 VA)</option>
                                <option value="B1" {{ $pelanggan->kategori_tarif == 'B1' ? 'selected' : '' }}>B1 (2200 VA)</option>
                            </select>
                            <small class="text-muted">Pilih kategori tarif listrik sesuai dengan daya yang digunakan.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Join</label>
                            <input type="date" name="tanggal_join" class="form-control" value="{{ $pelanggan->tanggal_join }}" required>
                            <small class="text-muted">Masukkan tanggal pelanggan bergabung.<br><strong>Contoh:</strong> 2023-05-10</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Pembayaran Terakhir</label>
                            <input type="date" name="tanggal_pembayaran_terakhir" class="form-control" value="{{ $pelanggan->tanggal_pembayaran_terakhir }}" readonly>
                            <small class="text-muted">Tanggal pembayaran terakhir tidak dapat diubah.<br><strong>Contoh:</strong> 2024-02-15</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">KWH Bulan Lalu</label>
                            <input type="number" name="kwh_bulan_lalu" class="form-control" value="{{ $pelanggan->kwh_bulan_lalu }}" readonly>
                            <small class="text-muted">Jumlah penggunaan KWH bulan lalu (otomatis dari sistem).<br><strong>Contoh:</strong> 120</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">KWH Terakhir</label>
                            <input type="number" name="kwh_terakhir" class="form-control" value="{{ $pelanggan->kwh_terakhir }}" required>
                            <small class="text-muted">Masukkan jumlah KWH terbaru sesuai dengan meteran pelanggan.<br><strong>Contoh:</strong> 150</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
