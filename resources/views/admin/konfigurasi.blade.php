@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-3xl font-bold mb-6 flex items-center">
            <i class="fas fa-cogs mr-2 text-blue-500"></i> Konfigurasi Sistem
        </h2>

        <!-- Tombol Kembali ke Dashboard -->
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

            <!-- Biaya Admin -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-cash-register mr-2 text-gray-600"></i> Biaya Admin (Rp)
                </label>
                <input type="number" name="biaya_admin" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                    value="{{ $konfigurasi->biaya_admin ?? 2500 }}" required>
            </div>

            <!-- Biaya Abodemen -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-file-invoice mr-2 text-gray-600"></i> Biaya Abodemen (Rp)
                </label>
                <input type="number" name="abodemen" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                    value="{{ $konfigurasi->abodemen ?? 10000 }}" required>
            </div>

            <!-- Denda Bulanan -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-gray-600"></i> Denda Bulanan (Rp)
                </label>
                <input type="number" name="denda_bulanan" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                    value="{{ $konfigurasi->denda_bulanan ?? 5000 }}" required>
            </div>

            <!-- Ubah Tarif per KWH -->
            <div class="mb-4">
                <label class="font-semibold flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i> Tarif per KWH (Rp)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700">R1 (450 VA)</label>
                        <input type="number" name="tarif_per_kwh[R1]" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                            value="{{ $konfigurasi->tarif_per_kwh['R1'] ?? 1500 }}" required>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">R2 (900 VA)</label>
                        <input type="number" name="tarif_per_kwh[R2]" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                            value="{{ $konfigurasi->tarif_per_kwh['R2'] ?? 2000 }}" required>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">R3 (1300 VA)</label>
                        <input type="number" name="tarif_per_kwh[R3]" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                            value="{{ $konfigurasi->tarif_per_kwh['R3'] ?? 2500 }}" required>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">B1 (2200 VA)</label>
                        <input type="number" name="tarif_per_kwh[B1]" class="w-full p-2 border rounded-lg focus:ring focus:ring-blue-300" 
                            value="{{ $konfigurasi->tarif_per_kwh['B1'] ?? 3000 }}" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 hover:bg-blue-600 transition">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
