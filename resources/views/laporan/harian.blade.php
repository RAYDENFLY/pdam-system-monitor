@extends('layouts.app')

@section('content')
<div class="container">
<h2 class="text-3xl font-bold text-gray-800 text-center mb-6">
    Laporan <span class="text-blue-600">Harian</span>
</h2>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('laporan.harian') }}" class="mb-4">
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

    <!-- Total Pemasukan & Pengeluaran -->
    <div class="alert alert-info mb-4">
        <strong>Total Pemasukan: </strong> Rp {{ number_format($total_pemasukan, 0, ',', '.') }}
    </div>
    <div class="alert alert-danger mb-4">
        <strong>Total Pengeluaran: </strong> Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}
    </div>

    <!-- Tabel Pemasukan -->
    <h4 class="mt-5 mb-3">Laporan Pemasukan</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Pelanggan</th>
                    <th>Total Tagihan</th>
                    <th>Denda</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembayarans as $pembayaran)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-m-Y') }}</td>
                    <td>{{ $pembayaran->nomor_pelanggan }}</td>
                    <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pembayaran->denda, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tabel Pengeluaran -->
    <h4 class="mt-5 mb-3">Laporan Pengeluaran</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengeluarans as $pengeluaran)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-m-Y') }}</td>
                    <td>Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $pengeluaran->keterangan }}</td>
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

    <!-- Script Export CSV -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
    <script>
    document.getElementById("exportCsv").addEventListener("click", function () {
        let csvData = [];
        let tanggalMulai = document.querySelector("input[name='tanggal_mulai']").value;
        let tanggalSelesai = document.querySelector("input[name='tanggal_selesai']").value;

        // Header
        csvData.push(['Laporan Harian']);
        csvData.push([`Periode: ${tanggalMulai} - ${tanggalSelesai}`]);
        csvData.push([]);

        // Pemasukan
        csvData.push(['Pemasukan']);
        csvData.push(['Tanggal', 'Nomor Pelanggan', 'Total Tagihan', 'Denda', 'Total Pembayaran']);

        document.querySelectorAll(".table-responsive table tbody tr").forEach(row => {
            let cols = row.querySelectorAll("td");
            if (cols.length === 5) {
                csvData.push([
                    cols[0].innerText.trim(),
                    cols[1].innerText.trim(),
                    cols[2].innerText.trim(),
                    cols[3].innerText.trim(),
                    cols[4].innerText.trim()
                ]);
            }
        });

        csvData.push([]);

        // Pengeluaran
        csvData.push(['Pengeluaran']);
        csvData.push(['Tanggal', 'Jumlah', 'Keterangan']);

        document.querySelectorAll(".table-responsive table:nth-of-type(2) tbody tr").forEach(row => {
            let cols = row.querySelectorAll("td");
            csvData.push([
                cols[0].innerText.trim(),
                cols[1].innerText.trim(),
                cols[2].innerText.trim()
            ]);
        });

        // Convert ke CSV
        let csv = Papa.unparse(csvData, { delimiter: ",", quotes: true });
        let blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });

        // Download file
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Laporan_Harian.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    </script>

</div>
@endsection
