@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700">Edit Pengeluaran</h2>
        <a href="{{ route('pengeluaran.index') }}" class="bg-gray-700 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-800 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-600 mb-4">Form Edit Pengeluaran</h3>
        <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Tanggal Pengeluaran</label>
                <input type="date" name="tanggal" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200" 
                    value="{{ $pengeluaran->tanggal }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Jumlah Pengeluaran</label>
                <div class="flex items-center border rounded-lg px-4 py-2 focus-within:ring focus-within:ring-blue-200">
                    <span class="text-gray-600">Rp</span>
                    <input type="text" name="jumlah" id="jumlah" 
                        class="w-full px-2 py-1 border-none focus:ring-0 outline-none" 
                        value="{{ number_format($pengeluaran->jumlah, 0, ',', '.') }}" required oninput="formatRupiah(this)">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Keterangan</label>
                <input type="text" name="keterangan" class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200" 
                    value="{{ $pengeluaran->keterangan }}">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
function formatRupiah(input) {
    let value = input.value.replace(/\D/g, "");
    let formatted = new Intl.NumberFormat("id-ID").format(value);
    input.value = formatted;
}
</script>
@endsection
