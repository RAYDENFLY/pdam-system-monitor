@extends('layouts.app')

@section('content')
<div class="container mx-auto">

    <!-- Header -->
    <header class="flex justify-between items-center py-4 px-6 bg-white shadow-md">
        <img alt="PDAM logo" class="h-12" src="https://storage.googleapis.com/a1aa/image/WXYgjGg90ZKKyo5DFbFcTSufDygjHN9dZGxkHCGyVL8.jpg"/>
        <nav class="flex gap-6">
            <a class="text-gray-700 font-medium hover:text-blue-600 transition duration-300" href="{{ url('/') }}">Home</a>
            <a class="text-gray-700 font-medium hover:text-blue-600 transition duration-300" href="#cek-tagihan">Cek Tagihan</a>
            <a class="text-gray-700 font-medium hover:text-blue-600 transition duration-300" href="#kontak">Kontak</a>
            @auth
                <a class="bg-green-500 text-white py-2 px-5 rounded-lg shadow-md hover:bg-green-600 transition duration-300" href="{{ url('/dashboard') }}">Dashboard</a>
            @else
                <a class="bg-blue-500 text-white py-2 px-5 rounded-lg shadow-md hover:bg-blue-600 transition duration-300" href="{{ route('login') }}">Login</a>
            @endauth
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-20 text-center">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-5xl font-extrabold">SISTEM INFORMASI PEMBAYARAN TAGIHAN AIR</h1>
            <p class="text-lg mt-4 opacity-90">Kelola dan bayar tagihan air dengan cepat, aman, dan efisien.</p>
            <a href="#cek-tagihan" class="mt-6 inline-block bg-white text-blue-600 font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-gray-200 transition duration-300">
                Cek Tagihan Sekarang
            </a>
        </div>
    </section>

    <!-- Cek Tagihan -->
    <section id="cek-tagihan" class="bg-gray-50 py-16 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Cek Tagihan Anda</h2>
        <p class="text-lg text-gray-600 mt-2">Masukkan nomor pelanggan untuk melihat tagihan Anda</p>

        <div class="mt-6 flex justify-center">
            <form onsubmit="return redirectToInvoice()" class="flex gap-4 bg-white p-4 rounded-lg shadow-md">
                <input id="nomor_pelanggan" class="p-3 w-80 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Masukkan Nomor Pelanggan" type="text" required/>
                <button type="submit" class="bg-blue-500 text-white py-3 px-6 rounded-md font-semibold shadow-md hover:bg-blue-600 transition duration-300">
                    Cek Tagihan
                </button>
            </form>
        </div>
    </section>

    <!-- Kontak Kami -->
    <section id="kontak" class="bg-white py-16 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Kontak Kami</h2>
        <p class="text-lg text-gray-600 mt-2">Terhubung lebih dekat dengan kami</p>

        <div class="grid md:grid-cols-3 gap-8 mt-8 max-w-4xl mx-auto text-left">
            <div class="p-6 bg-gray-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Lokasi</h3>
                <p class="text-gray-600">Desa Kalijero, Kec. Wiro, Kab. Bogan, Prov. Jawa Utara, 58192</p>
            </div>
            <div class="p-6 bg-gray-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">Email</h3>
                <p class="text-gray-600">pdam@pdam.com</p>
            </div>
            <div class="p-6 bg-gray-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700">No. HP</h3>
                <p class="text-gray-600">0912-3456-7890</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-6 text-center">
        <p class="text-sm">© {{ date('Y') }} PDAM - Semua Hak Dilindungi.</p>
    </footer>
</div>

<script>
    function redirectToInvoice() {
        let nomorPelanggan = document.getElementById("nomor_pelanggan").value;
        if (nomorPelanggan.trim() === '') {
            alert("Harap masukkan nomor pelanggan!");
            return false;
        }
        window.location.href = "{{ route('invoice.show', '') }}/" + nomorPelanggan;
        return false;
    }
</script>
@endsection
