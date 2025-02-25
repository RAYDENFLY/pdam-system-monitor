@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700">Data Pengeluaran</h2>
        <a href="{{ route('dashboard') }}" class="bg-gray-700 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-800 transition">
            <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Form Tambah Pengeluaran -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-600 mb-4">Tambah Pengeluaran</h3>
            <form method="POST" action="{{ route('pengeluaran.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Tanggal Pengeluaran</label>
                    <input type="date" name="tanggal" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
                </div>

                                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Jumlah Pengeluaran</label>
                    <div class="flex items-center border rounded-lg px-4 py-2 focus-within:ring focus-within:ring-blue-200">
                        <span class="text-gray-600">Rp</span>
                        <input type="text" id="formattedJumlah" 
                            class="w-full px-2 py-1 border-none focus:ring-0 outline-none" 
                            required oninput="formatRupiah(this)">
                        <input type="hidden" name="jumlah" id="jumlah">
                    </div>
                </div>


                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
                </button>
            </form>

            <!-- Keterangan Cara Mengisi Form -->
            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                <h4 class="text-gray-600 font-semibold mb-2">📌 Cara Mengisi Form:</h4>
                <ul class="list-disc list-inside text-sm text-gray-600">
                    <li><strong>Tanggal Pengeluaran:</strong> Pilih tanggal kapan pengeluaran terjadi. <br> <span class="text-gray-500">Contoh: 2024-02-25</span></li>
                    <li><strong>Jumlah Pengeluaran:</strong> Masukkan jumlah uang yang dikeluarkan. Hanya angka diperbolehkan. <br> <span class="text-gray-500">Contoh: 500000 (tanpa titik/koma)</span></li>
                    <li><strong>Keterangan:</strong> Jelaskan pengeluaran tersebut digunakan untuk apa. <br> <span class="text-gray-500">Contoh: Pembelian alat tulis kantor</span></li>
                </ul>
            </div>
        </div>

        <!-- Tabel Pengeluaran -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-600 mb-4">Daftar Pengeluaran</h3>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Jumlah</th>
                            <th class="px-4 py-2 border">Keterangan</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengeluarans as $pengeluaran)
                        <tr class="text-center border-b">
                            <td class="px-4 py-2 border">{{ date('d M Y', strtotime($pengeluaran->tanggal)) }}</td>
                            <td class="px-4 py-2 border">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 border">{{ $pengeluaran->keterangan }}</td>
                            <td class="px-4 py-2 border flex justify-center gap-2">
                                <a href="{{ route('pengeluaran.edit', $pengeluaran->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($pengeluarans->isEmpty())
                    <p class="text-center text-gray-500 mt-3">Belum ada data pengeluaran.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Script Format Rupiah -->
<script>
function formatRupiah(input) {
    let value = input.value.replace(/\D/g, ""); // Hanya angka
    let formatted = new Intl.NumberFormat("id-ID").format(value);
    
    input.value = formatted; // Format tampilan
    document.getElementById('jumlah').value = value; // Simpan angka asli
}
</script>

@endsection
