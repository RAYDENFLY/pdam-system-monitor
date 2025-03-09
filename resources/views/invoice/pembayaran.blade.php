@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pembayaran Tagihan Listrik</h2>

    <!-- Pilih Pelanggan -->
    <form action="{{ route('invoice.prosesBayar') }}" method="GET">
        <div class="mb-3">
            <label for="nomor_pelanggan" class="form-label">Pilih Pelanggan</label>
            <select class="form-control" id="nomor_pelanggan" name="nomor_pelanggan" required onchange="this.form.submit()">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->nomor_pelanggan }}" 
                        {{ isset($nomorPelanggan) && $nomorPelanggan == $pelanggan->nomor_pelanggan ? 'selected' : '' }}>
                        {{ $pelanggan->nomor_pelanggan }} - {{ $pelanggan->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Menampilkan Tagihan -->
    @if(isset($totalTagihan))
    <div class="card my-4">
        <div class="card-header">
            <h4>Detail Tagihan</h4>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">Nama Pelanggan:</div>
                <div class="col-md-8"><strong>{{ $pelanggan->nama }}</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Nomor Pelanggan:</div>
                <div class="col-md-8"><strong>{{ $pelanggan->nomor_pelanggan }}</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Total Pemakaian:</div>
                <div class="col-md-8"><strong>{{ $total_pemakaian }} kWh</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Total Tagihan:</div>
                <div class="col-md-8"><strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Denda:</div>
                <div class="col-md-8"><strong>Rp {{ number_format($total_denda, 0, ',', '.') }}</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Biaya Admin:</div>
                <div class="col-md-8"><strong>Rp {{ number_format($biaya_admin, 0, ',', '.') }}</strong></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">Biaya Abodemen:</div>
                <div class="col-md-8"><strong>Rp {{ number_format($pembayaranTerakhir->biaya_abodemen ?? 0, 0, ',', '.') }}</strong></div>
            </div>
            @php
        $total_pembayaran = $totalTagihan + $biaya_admin + ($pembayaranTerakhir->biaya_abodemen ?? 0) + $total_denda;
        @endphp

            <div class="row mb-2">
                <div class="col-md-4"><strong>Total Pembayaran:</strong></div>
                <div class="col-md-8"><strong>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</strong></div>

            </div>
        </div>
    </div>

    <!-- Input Pembayaran -->
    <div class="card my-4">
        <div class="card-header">
            <h4>Pembayaran</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('invoice.bayar') }}" method="POST">
                @csrf
                <input type="hidden" name="nomor_pelanggan" value="{{ $nomorPelanggan }}">
                <input type="hidden" name="total_bayar" value="{{ $total_pembayaran }}">

                <div class="mb-3">
                    <label for="jumlah_dibayar" class="form-label">Jumlah Dibayarkan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="jumlah_dibayar" name="jumlah_dibayar" required min="{{ $total_pembayaran }}">
                    </div>
                </div>

                <!-- Menampilkan Kembalian -->
                <div class="mb-3">
                    <label class="form-label">Kembalian</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="kembalian" readonly value="0">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Bayar</button>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
document.getElementById('jumlah_dibayar')?.addEventListener('input', function() {
    // Only run this calculation if the element exists and we have data
    @if(isset($total_pembayaran))
    let totalBayar = {{ $total_pembayaran }};
    let jumlahDibayar = parseFloat(this.value) || 0;
    let kembalian = jumlahDibayar - totalBayar;
    document.getElementById('kembalian').value = kembalian > 0 ? kembalian.toLocaleString('id-ID') : 0;
    @endif
});
</script>
@endsection