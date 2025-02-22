@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Buat Akun Baru</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.storeUser') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
                <option value="teknisi">Teknisi</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Daftar</button>
        <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Kembali</a>
    </form>

    <!-- Daftar Karyawan -->
    <h2 class="mt-5">Daftar Karyawan</h2>

    <!-- Filter Role -->
    <form method="GET" action="{{ route('admin.register') }}" class="mb-3">
        <label class="form-label">Filter berdasarkan Role:</label>
        <select name="filter_role" class="form-select w-auto d-inline">
            <option value="">Semua</option>
            <option value="admin" {{ request('filter_role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="kasir" {{ request('filter_role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
            <option value="teknisi" {{ request('filter_role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>
                    <!-- Tombol Edit -->
                    <a href="{{ route('admin.editUser', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus karyawan ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
