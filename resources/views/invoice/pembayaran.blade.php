@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600"><i class="fas fa-file-invoice-dollar"></i> Buat Invoice Baru</h1>
                <button class="bg-orange-500 text-white px-4 py-2 rounded">Reset Form</button>
            </div>
            <form action="{{ route('invoice.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2"><i class="fas fa-users"></i> Pilih Pelanggan</label>
                            <select id="selectPelanggan" name="nomor_pelanggan" class="w-full px-3 py-2 border rounded">
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
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2"><i class="fas fa-user"></i> Nama</label>
                            <input type="text" id="nama" name="nama" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2"><i class="fas fa-bolt"></i> KWH Bulan Lalu</label>
                            <input type="number" id="kwh_bulan_lalu" name="kwh_bulan_lalu" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2"><i class="fas fa-plug"></i> KWH Terakhir</label>
                            <input type="number" id="kwh_terakhir" name="kwh_terakhir" class="w-full px-3 py-2 border rounded" required>
                        </div>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded">Cari Data</button>
                    </div>
                    <div>
                        <div class="bg-orange-400 text-white p-4 rounded mb-4">
                            <h2 class="text-xl font-bold">Sub-Total : Rp {{ number_format($total_tagihan + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0) + ($pembayaranTerakhir->denda ?? 0), 0, ',', '.') }}</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-bold">Deskripsi</h3>
                                <p>Penggunaan KWH</p>
                                <p>Tarif per KWH</p>
                                <p>Tagihan Listrik</p>
                                <p>Biaya Admin</p>
                                <p>Biaya Abodemen</p>
                                <p>Denda</p>
                                <p class="font-bold">Total</p>
                            </div>
                            <div>
                                <h3 class="font-bold">Jumlah</h3>
                                <p id="total_pemakaian_display">{{ $total_pemakaian }}</p>
                                <p> {{ $tarif_daya ?? '-' }}</p>
                                <p>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</p>
                                <p>Rp {{ number_format($pembayaranTerakhir->denda ?? 0, 0, ',', '.') }}</p>
                                <p>Rp {{ number_format($pembayaranTerakhir->biaya_abodemen ?? 0, 0, ',', '.') }}</p>
                                <p>Rp {{ number_format($pembayaranTerakhir->denda ?? 0, 0, ',', '.') }}</p>
                                <p>Rp {{ number_format($total_tagihan + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0) + ($pembayaranTerakhir->denda ?? 0), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Penggunaan Awal (KWH)</label>
                        <input type="text" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Penggunaan Akhir (KWH)</label>
                        <input type="text" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Total Penggunaan (KWH)</label>
                        <input type="text" id="total_pemakaian" name="total_pemakaian" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Masukan uang Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full px-3 py-2 border rounded" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Kembalian</label>
                        <input type="text" class="w-full px-3 py-2 border rounded bg-gray-200" readonly placeholder="Rp">
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Buat Invoice</button>
                </div>
            </form>
        </div>
    </div>

    
@endsection
