@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-3xl font-bold mb-6 flex items-center">
            <i class="fas fa-cogs mr-2 text-blue-500"></i> Konfigurasi Sistem
        </h2>

        <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>

        @if(session('success'))
            <div class="bg-green-500 text-white p-3 rounded-lg mb-4 flex items-center">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('konfigurasi.update') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Denda Bulanan -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-gray-600"></i> Denda Bulanan (Rp)
                </label>
                <input type="number" name="denda_bulanan" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                    value="{{ $konfigurasi->denda_bulanan ?? 5000 }}" required>
            </div>

            <!-- Tarif per KWH -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i> Tarif per KWH (Rp)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['R1' => 1500, 'R2' => 2000, 'R3' => 2500, 'B1' => 3000] as $kategori => $default)
                        <div>
                            <label class="block font-medium text-gray-700">{{ $kategori }} </label>
                            <input type="number" name="tarif_per_kwh[{{ $kategori }}]" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                                value="{{ $konfigurasi->tarif_per_kwh[$kategori] ?? $default }}" required>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 hover:bg-blue-600 transition">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
