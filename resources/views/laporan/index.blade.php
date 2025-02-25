@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Laporan Keuangan</h2>

    <!-- Form Filter Tanggal -->
    <form method="GET" action="{{ route('laporan.index') }}" class="mb-4">
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

    <!-- Total Pemasukan -->
    <div class="alert alert-info mb-4">
        <strong>Total Pemasukan: </strong> Rp {{ number_format($total_pemasukan, 0, ',', '.') }}
    </div>
    <div class="alert alert-danger mb-4">
        <strong>Total Pengeluaran: </strong> Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}
    </div>
    <!-- Tabel Laporan Keuangan -->
    <h4 class="mt-5 mb-3">Laporan Pemasukan</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Pelanggan</th>
                    <th>Total Pemakaian</th>
                    <th>Total Tagihan</th>
                    <th>Jumlah Dibayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembayarans as $pembayaran)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('laporan.show', $pembayaran->nomor_pelanggan) }}" class="text-primary font-weight-bold">
                            {{ $pembayaran->nomor_pelanggan }}
                        </a>
                    </td>
                    <td>{{ number_format($pembayaran->total_pemakaian, 0, ',', '.') }} KWH</td>
                    <td>Rp {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_dibayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>



        <script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("exportCsv").addEventListener("click", function () {
        let csvData = [];
        let tanggalMulai = document.querySelector("input[name='tanggal_mulai']").value;
        let tanggalSelesai = document.querySelector("input[name='tanggal_selesai']").value;
        let periode = `Periode: ${tanggalMulai} - ${tanggalSelesai}`;
        // Header dan periode
        csvData.push(['Laporan Keuangan']);
        csvData.push([`Periode:\t${tanggalMulai} - ${tanggalSelesai}`]);
        csvData.push([]);

        // Header pemasukan
        csvData.push(['Pemasukan']);
        csvData.push(['Tanggal', 'Nomor Pelanggan', 'Total Pemakaian', 'Total Tagihan', 'Jumlah Dibayar']);

        let tables = document.querySelectorAll(".table-responsive table");
        let pemasukanTable = tables[0];
        let pemasukanRows = pemasukanTable.querySelectorAll("tbody tr");

        let totalPemasukan = 0;
        pemasukanRows.forEach(row => {
            let cols = row.querySelectorAll("td");
            let jumlahDibayar = cols[4]?.innerText.trim().replace('Rp ', '').replace(/\./g, '') || "0";
            totalPemasukan += parseInt(jumlahDibayar);

            csvData.push([
                cols[0]?.innerText.trim() || "",
                cols[1]?.innerText.trim() || "",
                cols[2]?.innerText.trim() || "",
                cols[3]?.innerText.trim() || "",
                cols[4]?.innerText.trim() || "",
            ]);
        });

        // Total pemasukan
        csvData.push(['Total', `Rp${totalPemasukan.toLocaleString('id-ID')}`]);
        csvData.push([]);

        // Header pengeluaran
        csvData.push(['Pengeluaran']);
        csvData.push(['Tanggal', 'Jumlah', 'Keterangan']);

        let pengeluaranTable = tables[1];
        let pengeluaranRows = pengeluaranTable.querySelectorAll("tbody tr");
        pengeluaranRows.forEach(row => {
            let cols = row.querySelectorAll("td");
            csvData.push([
                cols[0]?.innerText.trim() || "",
                cols[1]?.innerText.trim() || "",
                cols[2]?.innerText.trim() || "",
            ]);
        });

        // Convert array ke CSV pakai PapaParse
        let csv = Papa.unparse(csvData, {
            delimiter: ",",
            quotes: true
        });

        // Tambahkan BOM agar Excel membaca UTF-8 dengan benar
        let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
        let blob = new Blob([bom, csv], { type: "text/csv;charset=utf-8;" });

        // Download file
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Laporan_Keuangan.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
        </script>



@endsection
