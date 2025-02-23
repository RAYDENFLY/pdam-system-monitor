@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">

    {{-- Tombol Kembali ke Dashboard --}}
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-6">Tambah Karyawan</h2>

    {{-- Notifikasi sukses / error --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Form Tambah Karyawan --}}
    <form action="{{ route('admin.storeUser') }}" method="POST" class="bg-white shadow-lg rounded-lg p-6 mb-6 w-full">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Nama</label>
            <input type="text" name="name" class="w-full p-3 border rounded-lg @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" class="w-full p-3 border rounded-lg @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" name="password" class="w-full p-3 border rounded-lg @error('password') border-red-500 @enderror" required>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full p-3 border rounded-lg" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block text-gray-700 font-medium mb-2">Role</label>
            <select name="role" class="w-full p-3 border rounded-lg @error('role') border-red-500 @enderror" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
                <option value="teknisi">Teknisi</option>
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-user-plus"></i> Tambah Karyawan
        </button>
    </form>

    <hr class="my-6">

    {{-- Filter berdasarkan role --}}
    <form method="GET" action="{{ route('admin.register') }}" class="mb-6">
        <label for="filter_role" class="text-gray-700 font-medium mb-2">Filter berdasarkan Role:</label>
        <select name="filter_role" class="w-full md:w-1/3 p-3 border rounded-lg" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="admin" {{ request('filter_role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="kasir" {{ request('filter_role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
            <option value="teknisi" {{ request('filter_role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
        </select>
    </form>

    {{-- Tabel Daftar Karyawan --}}
    <h2 class="text-xl font-bold mb-4">Daftar Karyawan</h2>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse bg-white shadow-lg rounded-lg">
            <thead>
                <tr class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="p-3">{{ $index + 1 }}</td>
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">{{ ucfirst($user->role) }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.editUser', $user->id) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Yakin ingin menghapus karyawan ini?')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($users->isEmpty())
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-500">Belum ada karyawan terdaftar.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
