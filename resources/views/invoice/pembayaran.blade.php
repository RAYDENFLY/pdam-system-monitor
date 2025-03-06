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
                    </div>
                    <div>
                        <div class="bg-orange-400 text-white p-4 rounded mb-4">
                            <h2  id="subtotal_pembayaran" class="text-xl font-bold">Sub-Total : Rp 0</h2>
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
                                <p id="tarif_per_kwh">> {{ $tarif_daya ?? '-' }}</p>
                                <p id="tagihan_listrik">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</p>
                                <p id="biaya_admin">Rp {{ number_format($pembayaranTerakhir->denda ?? 0, 0, ',', '.') }}</p>
                                <p id="biaya_abodemen">Rp {{ number_format($pembayaranTerakhir->biaya_abodemen ?? 0, 0, ',', '.') }}</p>
                                <p id="denda">Rp {{ number_format($pembayaranTerakhir->denda ?? 0, 0, ',', '.') }}</p>
                                <p id="total_pembayaran">Rp {{ number_format($total_tagihan + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0) + ($pembayaranTerakhir->denda ?? 0), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Penggunaan Awal (KWH)</label>
                    <input type="text" id="penggunaan_awal" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Penggunaan Akhir (KWH)</label>
                    <input type="text" id="penggunaan_akhir" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Total Penggunaan (KWH)</label>
                    <input type="text" id="total_pemakaian" class="w-full px-3 py-2 border rounded bg-gray-200" readonly>
                </div>
            </div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div>
        <label class="block text-gray-700 font-bold mb-2">Masukan uang Pelanggan <span class="text-red-500">*</span></label>
        <input type="number" id="masukan_uang" class="w-full px-3 py-2 border rounded" placeholder="Rp">
    </div>
    <div>
        <label class="block text-gray-700 font-bold mb-2">Kembalian</label>
        <input type="text" id="kembalian" class="w-full px-3 py-2 border rounded bg-gray-200" readonly placeholder="Rp">
    </div>
</div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Buat Invoice</button>
                </div>
            </form>
        </div>
    </div>

   <script>
document.addEventListener("DOMContentLoaded", function() {
    const selectPelanggan = document.getElementById("selectPelanggan");
    const namaInput = document.getElementById("nama");
    const alamatInput = document.getElementById("alamat");
    const kwhBulanLaluInput = document.getElementById("kwh_bulan_lalu");
    const kwhTerakhirInput = document.getElementById("kwh_terakhir");
    const totalPemakaianDisplay = document.getElementById("total_pemakaian_display");
    const totalPemakaianInput = document.getElementById("total_pemakaian");

    // Elemen tarif dan tagihan
    const tarifPerKwhDisplay = document.getElementById("tarif_per_kwh");
    const tagihanListrikDisplay = document.getElementById("tagihan_listrik");
    const biayaAdminDisplay = document.getElementById("biaya_admin");
    const biayaAbodemenDisplay = document.getElementById("biaya_abodemen");
    const dendaDisplay = document.getElementById("denda");
    const totalPembayaranDisplay = document.getElementById("total_pembayaran");
    const subtotalPembayaranDisplay = document.getElementById("subtotal_pembayaran"); // Elemen baru

    // Elemen tambahan
    const masukanUangInput = document.getElementById("masukan_uang");
    const kembalianInput = document.getElementById("kembalian");

    function hitungTagihan() {
        const kwhBulanLalu = parseFloat(kwhBulanLaluInput.value) || 0;
        const kwhTerakhir = parseFloat(kwhTerakhirInput.value) || 0;
        const totalPemakaian = Math.max(0, kwhTerakhir - kwhBulanLalu);

        // Update total pemakaian
        totalPemakaianDisplay.innerText = totalPemakaian;
        totalPemakaianInput.value = totalPemakaian;

        // Ambil tarif per KWH dari data pelanggan atau default
        const selectedOption = selectPelanggan.options[selectPelanggan.selectedIndex];
        const tarifPerKwh = parseFloat(selectedOption?.getAttribute("data-tarif")) || 1500;

        const biayaAdmin = 2500;
        const biayaAbodemen = 0;
        const denda = 0;

        // Hitung tagihan
        const totalTagihan = totalPemakaian * tarifPerKwh;
        const subtotalPembayaran = totalTagihan + biayaAbodemen + denda;
        const totalPembayaran = subtotalPembayaran + biayaAdmin;

        // Update tampilan subtotal dan total pembayaran
        tarifPerKwhDisplay.innerText = `Rp ${tarifPerKwh.toLocaleString("id-ID")}`;
        tagihanListrikDisplay.innerText = `Rp ${totalTagihan.toLocaleString("id-ID")}`;
        biayaAdminDisplay.innerText = `Rp ${biayaAdmin.toLocaleString("id-ID")}`;
        biayaAbodemenDisplay.innerText = `Rp ${biayaAbodemen.toLocaleString("id-ID")}`;
        dendaDisplay.innerText = `Rp ${denda.toLocaleString("id-ID")}`;
        subtotalPembayaranDisplay.innerText = `Sub-Total: Rp ${subtotalPembayaran.toLocaleString("id-ID")}`;
        totalPembayaranDisplay.innerText = `Rp ${totalPembayaran.toLocaleString("id-ID")}`;
        totalPembayaranDisplay.setAttribute("data-total", totalPembayaran);

        hitungKembalian(); // Pastikan kembalian selalu diperbarui
    }

    function hitungKembalian() {
        const masukanUang = parseFloat(masukanUangInput.value) || 0;
        const totalPembayaran = parseFloat(totalPembayaranDisplay.getAttribute("data-total")) || 0;
        const kembalian = Math.max(0, masukanUang - totalPembayaran);

        kembalianInput.value = `Rp ${kembalian.toLocaleString("id-ID")}`;
    }

    // Event listener saat pelanggan dipilih
    selectPelanggan.addEventListener("change", function() {
        const selectedOption = selectPelanggan.options[selectPelanggan.selectedIndex];

        if (selectedOption.value !== "") {
            namaInput.value = selectedOption.getAttribute("data-nama");
            alamatInput.value = selectedOption.getAttribute("data-alamat");
            kwhBulanLaluInput.value = selectedOption.getAttribute("data-kwh_bulan_lalu");
            kwhTerakhirInput.value = selectedOption.getAttribute("data-kwh_terakhir");

            hitungTagihan();
        } else {
            // Reset jika tidak ada pelanggan yang dipilih
            namaInput.value = "";
            alamatInput.value = "";
            kwhBulanLaluInput.value = "";
            kwhTerakhirInput.value = "";
            totalPemakaianDisplay.innerText = "0";
            totalPemakaianInput.value = "0";

            tarifPerKwhDisplay.innerText = "-";
            tagihanListrikDisplay.innerText = "Rp 0";
            biayaAdminDisplay.innerText = "Rp 2.500";
            biayaAbodemenDisplay.innerText = "Rp 0";
            dendaDisplay.innerText = "Rp 0";
            subtotalPembayaranDisplay.innerText = "Sub-Total: Rp 0";
            totalPembayaranDisplay.innerText = "Rp 0";
            totalPembayaranDisplay.setAttribute("data-total", "0");
        }
    });

    // Event listener saat KWH terakhir diubah
    kwhTerakhirInput.addEventListener("input", hitungTagihan);

    // Event listener saat uang dimasukkan
    masukanUangInput.addEventListener("input", hitungKembalian);
});

</script>


@endsection
