@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">

    <!-- Header with Sticky Navbar -->
    <header class="sticky top-0 z-50 bg-white shadow-md">
        <div class="flex justify-between items-center py-4 px-8 bg-gradient-to-r from-green-600 to-blue-700 text-white">
            <!-- Logo -->
            <img alt="Listrik Pasar Cipanas logo" class="h-12" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRikenMtJmVcTQ89zGHcZ6KLH90xKhLxknNHg&s"/>
            
            <!-- Navbar Links -->
            <nav class="hidden md:flex space-x-12 text-lg font-medium">
                <a href="{{ url('/') }}" class="hover:text-gray-200 transition duration-300">Home</a>
                <a href="#cek-tagihan" class="hover:text-gray-200 transition duration-300">Cek Tagihan</a>
                <a href="#kontak" class="hover:text-gray-200 transition duration-300">Kontak</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-green-600 hover:bg-green-700 py-2 px-6 rounded-lg transition duration-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 py-2 px-6 rounded-lg transition duration-300">Login</a>
                @endauth
            </nav>

            <!-- Mobile Menu Icon -->
            <div class="md:hidden flex items-center">
                <button id="menu-toggle" class="text-white text-3xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navbar (Hidden by Default) -->
        <div id="mobile-menu" class="md:hidden bg-gradient-to-r from-green-600 to-blue-700 text-white p-6 space-y-4">
            <a href="{{ url('/') }}" class="block hover:text-gray-200 transition duration-300">Home</a>
            <a href="#cek-tagihan" class="block hover:text-gray-200 transition duration-300">Cek Tagihan</a>
            <a href="#kontak" class="block hover:text-gray-200 transition duration-300">Kontak</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="block bg-green-600 hover:bg-green-700 py-2 px-6 rounded-lg transition duration-300">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block bg-blue-600 hover:bg-blue-700 py-2 px-6 rounded-lg transition duration-300">Login</a>
            @endauth
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-500 to-indigo-700 text-white py-20 text-center rounded-t-lg">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">Sistem Pembayaran Tagihan Listrik Pasar Cipanas</h1>
            <p class="text-lg sm:text-xl mt-4 opacity-90">Akses mudah untuk memeriksa dan membayar tagihan listrik dengan cepat, aman, dan efisien.</p>
            <a href="#cek-tagihan" class="mt-6 inline-block bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg shadow-md hover:bg-gray-100 transition duration-300">
                Cek Tagihan Sekarang
            </a>
        </div>
    </section>

    <!-- Cek Tagihan Section -->
    <section id="cek-tagihan" class="bg-gray-50 py-20 text-center">
        <h2 class="text-4xl font-semibold text-gray-800">Cek Tagihan Listrik Anda</h2>
        <p class="text-lg text-gray-600 mt-4">Masukkan nomor pelanggan untuk memeriksa tagihan listrik Anda</p>

        <div class="mt-10 flex justify-center">
            <form onsubmit="return redirectToInvoice()" class="flex gap-6 bg-white p-6 rounded-lg shadow-lg w-full sm:w-2/3 md:w-1/2 lg:w-1/3">
                <input id="nomor_pelanggan" class="p-4 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Nomor Pelanggan" type="text" required/>
                 <button type="submit" class="bg-blue-600 text-white py-4 px-6 rounded-md font-semibold shadow-md hover:bg-blue-700 transition duration-300 whitespace-nowrap">
                <i class="fas fa-search mr-3"></i> Cek Tagihan
            </button>

            </form>
        </div>
    </section>

    <!-- Kontak Kami Section -->
    <section id="kontak" class="bg-white py-20 text-center">
        <h2 class="text-4xl font-semibold text-gray-800">Kontak Kami</h2>
        <p class="text-lg text-gray-600 mt-4">Untuk pertanyaan lebih lanjut atau bantuan</p>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-12 mt-10 max-w-6xl mx-auto text-left">
            <div class="p-8 bg-gray-100 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-700 mb-3"><i class="fas fa-map-marker-alt text-green-500 mr-3"></i> Lokasi</h3>
                <p class="text-gray-600">Pasar Cipanas, Kecamatan Cipanas, Kabupaten Cianjur, Provinsi Jawa Barat, 43253</p>
            </div>
            <div class="p-8 bg-gray-100 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-700 mb-3"><i class="fas fa-envelope text-green-500 mr-3"></i> Email</h3>
                <p class="text-gray-600">listrikpasarcipanas@listrik.com</p>
            </div>
            <div class="p-8 bg-gray-100 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-700 mb-3"><i class="fas fa-phone-alt text-green-500 mr-3"></i> No. HP</h3>
                <p class="text-gray-600">0812-3456-7890</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 text-center">
        <p class="text-sm">© {{ date('Y') }} Listrik Pasar Cipanas - Semua Hak Dilindungi.</p>
    </footer>
</div>

<script>
    // Mobile Navbar Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

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
