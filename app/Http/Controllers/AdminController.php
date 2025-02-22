<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Menampilkan form register + daftar karyawan
    public function showRegisterForm(Request $request)
    {
        $filterRole = $request->query('filter_role'); // Ambil filter dari request
        $users = User::query();

        if ($filterRole) {
            $users->where('role', $filterRole);
        }

        return view('admin.register', [
            'users' => $users->get(),
        ]);
    }

    // Simpan user baru
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,kasir,teknisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.register')->with('success', 'User berhasil ditambahkan!');
    }

    // Menampilkan form edit user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    // Update data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,kasir,teknisi',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.register')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    // Hapus user
    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.register')->with('success', 'Karyawan berhasil dihapus.');
    }
}
