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
use App\Http\Controllers\KonfigurasiController;

Route::get('/', function () {
    return view('homepage');
});

Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/register', [AdminController::class, 'showRegisterForm'])->name('admin.register');
    Route::get('/admin/edit-user/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::post('/admin/update-user/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::delete('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::post('/admin/register', [AdminController::class, 'storeUser'])->name('admin.storeUser');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login')->with('success', 'Berhasil logout.');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Admin bisa akses semua halaman
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::resource('/pelanggan', PelangganController::class)->parameters([
        'pelanggan' => 'nomor_pelanggan'
    ]);
    Route::get('/pelanggan/{nomor_pelanggan}/invoice', [PelangganController::class, 'invoice'])->name('pelanggan.invoice');
});

Route::middleware(['auth', RoleMiddleware::class . ':kasir'])->group(function () {
    Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{nomor_pelanggan}', [LaporanKeuanganController::class, 'show'])->name('laporan.show');
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/buat-invoice', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/daftar-invoice', [InvoiceController::class, 'list'])->name('invoice.list');
    Route::get('/list-invoice', [InvoiceController::class, 'list'])->name('invoice.list');
    Route::get('/buat-invoice', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice/{id}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoice.markPaid');
    Route::post('/invoice/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoice.updateStatus');
    Route::post('/invoice/{id}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->middleware('admin')->name('invoice.markUnpaid');
    Route::post('/invoice/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoice.updateStatus');
    // Rute untuk export CSV
    Route::get('laporan/exportCSV', [LaporanKeuanganController::class, 'exportCSV'])->name('laporan.exportCSV');
    Route::get('/laporan/{nomor_pelanggan}/export-csv', [LaporanKeuanganController::class, 'exportCSV'])->name('laporan.exportCSV');
});

// ✅ Pembayaran bisa diakses oleh semua pengguna yang login
Route::middleware('auth')->group(function () {
    Route::post('/pembayaran/{nomor_pelanggan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{nomor_pelanggan}', [PembayaranController::class, 'show'])->name('pembayaran.history');
});

// ✅ Admin, Kasir, dan Teknisi bisa akses halaman pelanggan
Route::middleware(['auth', RoleMiddleware::class . ':admin,kasir,teknisi'])->group(function () {
    Route::get('/user/pelanggan', [PelangganController::class, 'index'])->name('user.pelanggan');
    Route::get('/pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
    Route::post('/pelanggan/store', [PelangganController::class, 'store'])->name('pelanggan.store');
});

// ✅ Invoice Routes
Route::get('/invoice/{nomor_pelanggan}', [InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/cetak-invoice/{nomor_pelanggan}', [InvoiceController::class, 'show'])->name('invoice.show');

// ✅ KWH Routes
Route::get('/kwh/create', [KwhController::class, 'create'])->name('kwh.create');
Route::post('/kwh/store', [KwhController::class, 'store'])->name('kwh.store');

// ✅ Konfigurasi (khusus admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/konfigurasi', [KonfigurasiController::class, 'index'])->name('konfigurasi.index');
    Route::post('/contact/update', [ContactController::class, 'update'])->name('contact.update');
    Route::post('/konfigurasi/update', [KonfigurasiController::class, 'update'])->name('konfigurasi.update');
});

Route::get('/get-denda/{nomor_pelanggan}', [InvoiceController::class, 'getDenda']);

require __DIR__.'/auth.php';
