<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdminController;



Route::get('/', function () {
    return view('homepage');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login')->with('success', 'Berhasil logout.');
})->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/register', [AdminController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/admin/register', [AdminController::class, 'storeUser'])->name('admin.storeUser');

    Route::get('/admin/edit/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::post('/admin/update/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::delete('/admin/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
});

Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::resource('/pelanggan', PelangganController::class)->parameters([
        'pelanggan' => 'nomor_pelanggan' // ✅ Pakai nomor pelanggan sebagai parameter
    ]);
    Route::get('/pelanggan/{nomor_pelanggan}/invoice', [PelangganController::class, 'invoice'])->name('pelanggan.invoice');
});

Route::middleware(['auth', RoleMiddleware::class . ':kasir'])->group(function () {
    Route::get('/kasir', [KasirController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
});

Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
Route::post('/pembayaran/{nomor_pelanggan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
Route::get('/pembayaran/{nomor_pelanggan}', [PembayaranController::class, 'show'])->name('pembayaran.history');

Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index');
Route::get('/history', [PelangganController::class, 'history'])->name('pelanggan.history');


// Menampilkan daftar invoice
Route::get('/daftar-invoice', [InvoiceController::class, 'list'])->name('invoice.list');

// Proses tandai lunas berdasarkan nomor pelanggan
Route::get('/invoice/{nomor_pelanggan}', [InvoiceController::class, 'show'])->name('invoice.show');

// Menampilkan daftar invoice
Route::get('/list-invoice', [InvoiceController::class, 'list'])->name('invoice.list');

// Menampilkan invoice pelanggan tertentu
Route::get('/cetak-invoice/{nomor_pelanggan}', [InvoiceController::class, 'show'])->name('invoice.show');

// Menampilkan form buat invoice
Route::get('/buat-invoice', [InvoiceController::class, 'create'])->name('invoice.create');

// Menyimpan invoice baru
Route::post('/buat-invoice', [InvoiceController::class, 'store'])->name('invoice.store');

// Memperbarui status invoice (lunas/belum lunas)
Route::post('/invoice/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoice.updateStatus');
// Menandai invoice sebagai lunas
Route::post('/invoice/{id}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoice.markPaid');

// Menandai invoice sebagai belum lunas (khusus admin)
Route::post('/invoice/{id}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])
    ->middleware('admin') // ✅ Pastikan ini ada
    ->name('invoice.markUnpaid');

Route::get('/pelanggan/{nomor_pelanggan}/history', [PelangganController::class, 'history'])->name('pelanggan.history');

    
require __DIR__.'/auth.php';
