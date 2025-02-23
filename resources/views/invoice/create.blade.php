@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-file-invoice-dollar"></i> Buat Invoice Baru</h2>

    <div class="card shadow-lg p-4">
        <form action="{{ route('invoice.store') }}" method="POST">
            @csrf

            <!-- Pilih Pelanggan -->
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-users"></i> Pilih Pelanggan</label>
                <select id="selectPelanggan" name="nomor_pelanggan" class="form-select">
                    <option value="" selected>-- Pilih Pelanggan --</option>
                    @foreach ($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->nomor_pelanggan }}" 
                            data-nama="{{ $pelanggan->nama }}" 
                            data-alamat="{{ $pelanggan->alamat }}" 
                            data-kwh_bulan_lalu="{{ $pelanggan->kwh_bulan_lalu }}" 
                            data-kwh_terakhir="{{ $pelanggan->kwh_terakhir }}">
                            {{ $pelanggan->nomor_pelanggan }} - {{ $pelanggan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nama & Alamat -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control" readonly>
                </div>
            </div>

            <!-- KWH -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-bolt"></i> KWH Bulan Lalu</label>
                    <input type="number" id="kwh_bulan_lalu" name="kwh_bulan_lalu" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-plug"></i> KWH Terakhir</label>
                    <input type="number" id="kwh_terakhir" name="kwh_terakhir" class="form-control" required>
                </div>
            </div>

            <!-- Total Pemakaian -->
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-chart-line"></i> Total Pemakaian (KWH)</label>
                <input type="number" id="total_pemakaian" name="total_pemakaian" class="form-control" readonly>
            </div>

            <!-- Total Tagihan -->
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-money-bill-wave"></i> Total Tagihan</label>
                <span id="total_tagihan_display" class="form-control">Rp 0</span>
                <input type="hidden" id="total_tagihan" name="total_tagihan">
            </div>

            <!-- Denda -->
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-exclamation-triangle"></i> Denda</label>
                <span id="denda_display" class="form-control">Rp 0</span>
                <input type="hidden" id="denda" name="denda">
            </div>

            <!-- Total Pembayaran -->
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-cash-register"></i> Total Pembayaran</label>
                <span id="total_pembayaran_display" class="form-control">Rp 0</span>
                <input type="hidden" id="total_pembayaran" name="total_pembayaran">
            </div>

            <button type="submit" class="btn btn-success mt-3">
                <i class="fas fa-save"></i> Buat Invoice
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('selectPelanggan').addEventListener('change', function() {
    let selected = this.options[this.selectedIndex];

    document.getElementById('nama').value = selected.dataset.nama || '';
    document.getElementById('alamat').value = selected.dataset.alamat || '';
    document.getElementById('kwh_bulan_lalu').value = selected.dataset.kwh_bulan_lalu || 0;
    document.getElementById('kwh_terakhir').value = selected.dataset.kwh_terakhir || 0;

    hitungTagihan();
});

document.getElementById('kwh_terakhir').addEventListener('input', hitungTagihan);

function hitungTagihan() {
    let kwhBulanLalu = parseInt(document.getElementById('kwh_bulan_lalu').value) || 0;
    let kwhTerakhir = parseInt(document.getElementById('kwh_terakhir').value) || 0;
    let totalPemakaian = Math.max(0, kwhTerakhir - kwhBulanLalu);
    
    document.getElementById('total_pemakaian').value = totalPemakaian;
    
    let tarifPerKwh = 1500;
    let totalTagihan = totalPemakaian * tarifPerKwh;

    document.getElementById('total_tagihan').value = totalTagihan;
    document.getElementById('total_tagihan_display').textContent = formatRupiah(totalTagihan);

    let nomorPelanggan = document.getElementById('selectPelanggan').value;
    if (nomorPelanggan) {
        fetch(`/get-denda/${nomorPelanggan}`)
            .then(response => response.json())
            .then(data => {
                // Ensure denda is always positive using Math.abs()
                let denda = Math.abs(data.denda || 0);
                document.getElementById('denda').value = denda;
                document.getElementById('denda_display').textContent = formatRupiah(denda);

                let totalPembayaran = totalTagihan + denda;
                document.getElementById('total_pembayaran').value = totalPembayaran;
                document.getElementById('total_pembayaran_display').textContent = formatRupiah(totalPembayaran);
            })
            .catch(error => {
                console.error('Error fetching denda:', error);
                document.getElementById('denda').value = 0;
                document.getElementById('denda_display').textContent = formatRupiah(0);

                document.getElementById('total_pembayaran').value = totalTagihan;
                document.getElementById('total_pembayaran_display').textContent = formatRupiah(totalTagihan);
            });
    }
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
}
</script>
@endsection
