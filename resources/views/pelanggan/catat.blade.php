@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
   <h2 class="text-white bg-blue-600 p-4 text-lg font-semibold text-center">
      Catat Pemakaian
   </h2>
   <div class="p-6">
      <form action="{{ route('pelanggan.catat.store') }}" method="POST">
         @csrf
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Pilih Pelanggan -->
            <div class="mb-4">
               <label class="block text-gray-700 font-semibold mb-1" for="nomor_pelanggan">
                  Pilih Nama Pelanggan <span class="text-red-500">*</span>
               </label>
               <select class="w-full border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200" id="nomor_pelanggan" name="nomor_pelanggan" required>
                  <option value="">-- Pilih Pelanggan --</option>
                  @foreach ($pelanggans as $pelanggan)
                     <option value="{{ $pelanggan->nomor_pelanggan }}" data-kwh_terakhir="{{ $pelanggan->kwh_terakhir }}">
                        {{ $pelanggan->nama }}
                     </option>
                  @endforeach
               </select>
            </div>

            <!-- Penggunaan Awal & Jumlah Penggunaan -->
            <div class="grid grid-cols-2 gap-4">
               <div>
                  <label class="block text-gray-700 font-semibold mb-1" for="penggunaan_awal">Penggunaan Awal</label>
                  <input class="w-full border border-gray-300 p-2 rounded-lg bg-gray-200" id="penggunaan_awal" name="penggunaan_awal" type="number" readonly required />
               </div>
               <div>
                  <label class="block text-gray-700 font-semibold mb-1" for="jumlah_penggunaan">Jumlah Penggunaan</label>
                  <input class="w-full border border-gray-300 p-2 rounded-lg bg-gray-200" id="jumlah_penggunaan" type="number" readonly />
               </div>
            </div>

            <!-- Alert -->
            <div class="col-span-2 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg" role="alert">
               <p>Form yang wajib diisi!</p>
            </div>

            <!-- Penggunaan Akhir -->
            <div>
               <label class="block text-gray-700 font-semibold mb-1" for="penggunaan_akhir">
                  Penggunaan Akhir <span class="text-red-500">*</span>
               </label>
               <input class="w-full border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200" id="penggunaan_akhir" name="penggunaan_akhir" type="number" required />
            </div>

            <!-- Pilih Bulan & Tahun -->
            <div class="flex gap-2">
               <select class="w-1/2 border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200" id="bulan_pemakaian" name="bulan_pemakaian" required>
                  <option value="">-- Pilih Bulan --</option>
                  @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                     <option value="{{ $bulan }}">{{ $bulan }}</option>
                  @endforeach
               </select>
               <select class="w-1/2 border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200" id="tahun_pemakaian" name="tahun_pemakaian" required>
                  <option value="{{ date('Y') }}">{{ date('Y') }}</option>
               </select>
            </div>
         </div>

         <!-- Tombol -->
         <div class="flex justify-between items-center mt-6">
            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
               Kembali
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
               Simpan
            </button>
         </div>
      </form>
   </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        const selectPelanggan = document.getElementById("nomor_pelanggan");
        if (!selectPelanggan) {
            console.error("Elemen select pelanggan tidak ditemukan!");
            return;
        }

        const penggunaanAwalInput = document.getElementById("penggunaan_awal");
        const penggunaanAkhirInput = document.getElementById("penggunaan_akhir");
        const totalPemakaianInput = document.getElementById("jumlah_penggunaan");

        selectPelanggan.addEventListener("change", function() {
            const selectedOption = selectPelanggan.options[selectPelanggan.selectedIndex];
            if (selectedOption.value !== "") {
                penggunaanAwalInput.value = selectedOption.getAttribute("data-kwh_terakhir");
                penggunaanAkhirInput.value = "";
                totalPemakaianInput.value = "0";
            } else {
                penggunaanAwalInput.value = "";
                penggunaanAkhirInput.value = "";
                totalPemakaianInput.value = "0";
            }
        });

        penggunaanAkhirInput.addEventListener("input", function() {
            const penggunaanAwal = parseFloat(penggunaanAwalInput.value) || 0;
            const penggunaanAkhir = parseFloat(penggunaanAkhirInput.value) || 0;

            if (penggunaanAkhir >= penggunaanAwal) {
                totalPemakaianInput.value = penggunaanAkhir - penggunaanAwal;
            } else {
                totalPemakaianInput.value = "0";
            }
        });
    }, 100);
});


</script>
@endsection
