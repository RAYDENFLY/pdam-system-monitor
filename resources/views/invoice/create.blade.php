@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Buat Invoice Baru</h2>

    <form action="{{ route('invoice.store') }}" method="POST">
        @csrf

        <!-- Pilih Pelanggan -->
        <div class="mb-3">
            <label class="form-label">Pilih Pelanggan</label>
            <select id="selectPelanggan" name="nomor_pelanggan" class="form-select">
                <option value="" selected>-- Pilih Pelanggan --</option>
                @foreach ($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->nomor_pelanggan }}" 
                        data-nama="{{ $pelanggan->nama }}" 
                        data-alamat="{{ $pelanggan->alamat }}" 
                        data-kwh-bulan-lalu="{{ $pelanggan->kwh_bulan_lalu }}" 
                        data-kwh-terakhir="{{ $pelanggan->kwh_terakhir }}">
                        {{ $pelanggan->nomor_pelanggan }} - {{ $pelanggan->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nama & Alamat -->
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="form-control" readonly>
        </div>

        <!-- KWH -->
        <div class="mb-3">
            <label class="form-label">KWH Bulan Lalu</label>
            <input type="number" id="kwh_bulan_lalu" name="kwh_bulan_lalu" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">KWH Terakhir</label>
            <input type="number" id="kwh_terakhir" name="kwh_terakhir" class="form-control" required>
        </div>

        <!-- Total Pemakaian -->
        <div class="mb-3">
            <label class="form-label">Total Pemakaian (KWH)</label>
            <input type="number" id="total_pemakaian" name="total_pemakaian" class="form-control" readonly>
        </div>

        <!-- Total Tagihan -->
        <div class="mb-3">
            <label class="form-label">Total Tagihan</label>
            <input type="number" id="total_tagihan" name="total_tagihan" class="form-control" readonly>
        </div>

        <!-- Denda -->
        <div class="mb-3">
            <label class="form-label">Denda</label>
            <input type="number" id="denda" name="denda" class="form-control" readonly>
        </div>

        <!-- Total Pembayaran -->
        <div class="mb-3">
            <label class="form-label">Total Pembayaran</label>
            <input type="number" id="total_pembayaran" name="total_pembayaran" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success mt-3">Buat Invoice</button>
    </form>
</div>

<script>
document.getElementById('selectPelanggan').addEventListener('change', function() {
    let selected = this.options[this.selectedIndex];
    
    document.getElementById('nama').value = selected.dataset.nama || '';
    document.getElementById('alamat').value = selected.dataset.alamat || '';
    document.getElementById('kwh_bulan_lalu').value = selected.dataset.kwhBulanLalu || 0;
    document.getElementById('kwh_terakhir').value = selected.dataset.kwhTerakhir || 0;

    hitungTagihan();
});

document.getElementById('kwh_terakhir').addEventListener('input', hitungTagihan);

function hitungTagihan() {
    let kwhBulanLalu = parseInt(document.getElementById('kwh_bulan_lalu').value) || 0;
    let kwhTerakhir = parseInt(document.getElementById('kwh_terakhir').value) || 0;
    let totalPemakaian = kwhTerakhir - kwhBulanLalu;
    
    document.getElementById('total_pemakaian').value = totalPemakaian;
    
    let tarifPerKwh = 1500;
    let totalTagihan = totalPemakaian * tarifPerKwh;
    document.getElementById('total_tagihan').value = totalTagihan;

    let denda = (new Date().getDate() > 20) ? 5000 : 0;
    document.getElementById('denda').value = denda;

    document.getElementById('total_pembayaran').value = totalTagihan + denda;
}
</script>
@endsection
