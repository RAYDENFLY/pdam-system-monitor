@extends('layouts.app')

@section('content')
<div class="container">
<h2 class="text-3xl font-bold text-gray-800 text-center mb-6">
    Laporan <span class="text-blue-600">Keseluruhan</span>
</h2>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('laporan.keseluruhan') }}" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Tabel Laporan Keseluruhan -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="laporanTable">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Pemasukan (Rp)</th>
                    <th>Pengeluaran (Rp)</th>
                    <th>Saldo Akhir (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $saldo = 0; @endphp
                @foreach ($laporanKeseluruhan as $laporan)
                @php 
                  $saldo += $laporan['pemasukan'] - $laporan['pengeluaran'];
                @endphp

                <tr>
                <td>{{ \Carbon\Carbon::parse($laporan['tanggal'])->format('d-m-Y') }}</td>
                <td>{{ $laporan['keterangan'] }}</td>
                <td>Rp {{ number_format($laporan['pemasukan'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($laporan['pengeluaran'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                 </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tombol Kembali dan Export -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-house-door"></i> Kembali ke Dashboard
        </a>
        <button id="exportCsv" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </button>
    </div>
</div>

<!-- Script untuk Export ke CSV -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
<script>
document.getElementById("exportCsv").addEventListener("click", function () {
    let table = document.getElementById("laporanTable");
    let rows = table.querySelectorAll("tr");
    let csvData = [];

    rows.forEach(row => {
        let rowData = [];
        row.querySelectorAll("td, th").forEach(cell => {
            rowData.push(cell.innerText);
        });
        csvData.push(rowData);
    });

    let csv = Papa.unparse(csvData);
    let blob = new Blob(["\ufeff" + csv], { type: "text/csv;charset=utf-8;" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Laporan_Keseluruhan.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});
</script>
@endsection
