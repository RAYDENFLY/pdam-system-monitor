<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Konfigurasi;
use Carbon\Carbon;
use App\Models\CatatanPemakaian;


class InvoiceController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.index', compact('pelanggans'));
    }

    function getTarif() {
        $bulanIni = date('Y-m'); // Ambil tahun-bulan sekarang (contoh: 2025-03)

        // Ambil konfigurasi terbaru sebelum atau pada bulan ini
        $tarif = Konfigurasi::whereDate('created_at', '<=', $bulanIni . '-20') 
                    ->orderBy('created_at', 'desc')
                    ->first();

        
        return $tarif ? $tarif->tarif_per_kwh : 1500; // Default 1500 jika tidak ada tarif di database
    }
                    
        function updateTarif(Request $request) {
            $bulanDepan = Carbon::now()->addMonth()->format('Y-m');

            Konfigurasi::create([
                'tarif_per_kwh' => $request->tarif_per_kwh,
                'bulan_berlaku' => $bulanDepan
            ]);

            return back()->with('success', 'Tarif diperbarui dan berlaku mulai bulan depan!');
        }


    public function show($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        
        // Ambil pembayaran terakhir
        $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->latest()
            ->first();
    
        // Ambil konfigurasi

        $konfigurasi = Konfigurasi::first();
        $tarif_per_kwh = $konfigurasi->tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;
        
        // Hitung total
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;
        
        // Pastikan denda selalu positif
        $total_denda = abs(Pembayaran::hitungDenda($nomor_pelanggan));
    
        // Pastikan total pembayaran dihitung dengan penambahan
        $totalPembayaran = $total_tagihan + $total_denda;

      
        $tarif_daya = $pelanggan->kategori_tarif . ' / ' . $tarif_per_kwh . ' per kWh';

    
        // Debug untuk melihat nilai-nilai
        \Log::info("Debug Nilai di show():");
        \Log::info("Total Tagihan: " . $total_tagihan);
        \Log::info("Total Denda: " . $total_denda);
        \Log::info("Total Pembayaran: " . $totalPembayaran);
    
        return view('invoice.show', compact(
            'pelanggan',
            'pembayaranTerakhir',
            'total_pemakaian',
            'total_tagihan',
            'total_denda',
            'totalPembayaran',
            'tarif_daya'
        ));
    }

    public function generateAll()
    {
        $konfigurasi = Konfigurasi::first();
        $pelanggans = Pelanggan::all();
        $invoices = [];

        foreach ($pelanggans as $pelanggan) {
            $tarif_per_kwh = $konfigurasi->tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;
            $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
            $total_tagihan = $total_pemakaian * $tarif_per_kwh;
            $total_denda = Pembayaran::hitungDenda($pelanggan->nomor_pelanggan);

            $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $pelanggan->nomor_pelanggan)
                ->latest()
                ->first();

            // Perbaikan pengecekan sudah terbayar
            $total_yang_harus_dibayar = $total_tagihan + $total_denda;
            $sudahTerbayar = $pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $total_yang_harus_dibayar;
            
            
            if (!$sudahTerbayar) {
                $invoices[] = [
                    'pelanggan' => $pelanggan,
                    'total_pemakaian' => $total_pemakaian,
                    'total_tagihan' => $total_tagihan,
                    'total_denda' => $total_denda,
                    'total_pembayaran' => $total_yang_harus_dibayar,
                    'pembayaran' => $pembayaranTerakhir
                ];
            }
        }

        return view('invoice.automatic', compact('invoices'));
    }

    public function updateStatus($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
    
        // Hitung total yang harus dibayar termasuk semua biaya
        $totalHarusDibayar = $pembayaran->total_tagihan +
            ($pembayaran->denda ?? 0) +
            ($pembayaran->biaya_admin ?? 0) +
            ($pembayaran->biaya_abodemen ?? 0);
    
        // Set jumlah_dibayar agar sesuai dengan total yang harus dibayar
        $pembayaran->jumlah_dibayar = $totalHarusDibayar;
        $pembayaran->tanggal_pembayaran = now();
        $pembayaran->save();
    
        // Perbarui tanggal pembayaran terakhir pelanggan
        $pelanggan = $pembayaran->pelanggan;
        $pelanggan->tanggal_pembayaran_terakhir = now();
        $pelanggan->save();
    
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }
    

    public function markUnpaid($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('invoice.list')->with('error', 'Anda tidak memiliki izin.');
        }

        $invoice = Pembayaran::findOrFail($id);
        $invoice->jumlah_dibayar = 0;
        $invoice->save();

        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil ditandai sebagai belum lunas.');
    }

    public function markPaidIndex(Request $request)
{
    $search = $request->input('search');
    $query = Pelanggan::with(['pembayarans' => function ($q) {
        $q->latest();
    }]);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%$search%")
              ->orWhere('nomor_pelanggan', 'like', "%$search%");
        });
    }

    $pelanggans = $query->get()->map(function ($pelanggan) {
        $pembayaranTerakhir = $pelanggan->pembayarans->first();
        
        if ($pembayaranTerakhir) {
            $total_yang_harus_dibayar = 
                $pembayaranTerakhir->total_tagihan +
                $pembayaranTerakhir->denda +
                ($pembayaranTerakhir->biaya_abodemen ?? 0) + // Pastikan abodemen dihitung
                ($pembayaranTerakhir->biaya_admin ?? 0); // Pastikan admin dihitung
            
            $jumlah_dibayar = $pembayaranTerakhir->jumlah_dibayar;

            $pelanggan->status_pembayaran = ($jumlah_dibayar >= $total_yang_harus_dibayar) ? 'lunas' : 'belum lunas';
        } else {
            $pelanggan->status_pembayaran = 'belum lunas';
        }

        return $pelanggan;
    });

    return view('invoice.mark_paid', compact('pelanggans'));
}


    

    public function create()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.create', compact('pelanggans'));
    }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nomor_pelanggan' => 'required|exists:pelanggans,nomor_pelanggan',
            'total_pemakaian' => 'required|numeric|min:0',
            'total_tagihan' => 'required|numeric|min:0',
            'biaya_admin' => 'nullable|numeric|min:0', // Bisa kosong, default 2500
            'biaya_abodemen' => 'nullable|numeric|min:0', // Bisa kosong
            'denda' => 'nullable|numeric',
        ]);

        if (!$request->has('denda')) {
            $validated['denda'] = Pembayaran::hitungDenda($request->nomor_pelanggan);
        }
    
        // Ambil pelanggan
        $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();
    
        $total_denda = abs($request->denda ?? Pembayaran::hitungDenda($request->nomor_pelanggan));
    
        // Log the denda calculation
        \Log::info("Creating invoice for customer: " . $request->nomor_pelanggan);
        \Log::info("Calculated denda: " . $total_denda);
    
        // Ambil biaya admin, default ke 2500 jika tidak diisi
        $biaya_admin = $request->biaya_admin ?? 2500;
        $biaya_abodemen = $request->biaya_abodemen ?? 0;
    
        // Hitung total pembayaran
        $total_pembayaran = $request->total_tagihan + $total_denda + $biaya_admin + $biaya_abodemen;
    
        // Simpan pembayaran baru
        $pembayaran = Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'biaya_admin' => $biaya_admin,
            'biaya_abodemen' => $biaya_abodemen,
            'jumlah_dibayar' => 0,  // Belum dibayar
            'tanggal_pembayaran' => null, // Belum dibayar
            'denda' => $total_denda,  // Make sure this is saved
        ]);

        
        
        
        // Double-check the save was successful
        \Log::info("Saved invoice with ID: " . $pembayaran->id);
        \Log::info("Saved denda value: " . $pembayaran->denda);
        
        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dibuat.');
    }
    public function destroy($id)
{
    $invoice = Pembayaran::findOrFail($id);

    // Pastikan hanya admin yang bisa menghapus
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('invoice.list')->with('error', 'Anda tidak memiliki izin untuk menghapus invoice.');
    }

    $invoice->delete();
    return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dihapus.');
}

public function list(Request $request)
{
    $search = $request->input('search');
    $query = Pembayaran::with('pelanggan');

    if ($search) {
        $query->whereHas('pelanggan', function ($q) use ($search) {
            $q->where('nomor_pelanggan', 'like', "%$search%")
                ->orWhere('nama', 'like', "%$search%");
        });
    }

    $invoices = $query->get();
    foreach ($invoices as $invoice) {
        \Log::info("Invoice ID: " . $invoice->id . ", Denda: " . $invoice->denda);
    }
    
    return view('invoice.list', compact('invoices'));
}

public function bayar(Request $request)
{
    \Log::info('Fungsi bayar() dipanggil', ['request' => $request->all()]);

    $request->validate([
        'nomor_pelanggan' => 'required|exists:pelanggans,nomor_pelanggan',
        'total_bayar' => 'required|numeric|min:0',
        'jumlah_dibayar' => 'required|numeric|min:0',
    ]);

    \Log::info('Validasi berhasil', ['data' => $request->all()]);

    $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->first();
    if (!$pelanggan) {
        \Log::error('Pelanggan tidak ditemukan!', ['nomor_pelanggan' => $request->nomor_pelanggan]);
        return back()->with('error', 'Pelanggan tidak ditemukan.');
    }

    \Log::info('Pelanggan ditemukan', ['pelanggan' => $pelanggan]);

    // Ambil data pemakaian pelanggan
    $pemakaians = CatatanPemakaian::where('nomor_pelanggan', $request->nomor_pelanggan)->get();
    $total_pemakaian = $pemakaians->sum('jumlah_penggunaan') ?? 0;

    // Hitung denda
    $total_denda = abs(Pembayaran::hitungDenda($request->nomor_pelanggan));
    \Log::info('Denda dihitung', ['total_denda' => $total_denda]);

    // Pastikan biaya admin dan abodemen memiliki nilai
    $biaya_admin = $request->biaya_admin ?? 2500;
    
    // Cari data pembayaran terakhir untuk mendapatkan biaya abodemen
    $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $request->nomor_pelanggan)
                                   ->orderBy('id', 'desc')
                                   ->first();
    
    $biaya_abodemen = $pembayaranTerakhir->biaya_abodemen ?? 0;

    // Hitung total yang harus dibayar
    $total_pembayaran = $request->total_tagihan + $total_denda + $biaya_admin + $biaya_abodemen;

    \Log::info('Total pembayaran dari form', ['total_pembayaran' => $total_pembayaran]);

    $jumlah_dibayar = $request->jumlah_dibayar;
    $kembalian = max(0, $jumlah_dibayar - $total_pembayaran);
    if ($kembalian > $jumlah_dibayar) {
        \Log::error('Nilai kembalian tidak logis', ['kembalian' => $kembalian]);
        return back()->with('error', 'Terjadi kesalahan perhitungan kembalian.');
    }
    
    \Log::info('Total sebelum simpan', [
        'total_tagihan' => $request->total_tagihan,
        'total_denda' => $total_denda,
        'biaya_admin' => $biaya_admin,
        'biaya_abodemen' => $biaya_abodemen,
        'total_pembayaran' => $total_pembayaran,
    ]);
    
    
    if ($jumlah_dibayar < $total_pembayaran) {
        \Log::error('Pembayaran gagal: Uang kurang', [
            'jumlah_dibayar' => $jumlah_dibayar,
            'total_pembayaran' => $total_pembayaran
        ]);
        return back()->with('error', 'Uang yang dibayarkan kurang dari jumlah tagihan.');
    }

    try {
        // Simpan pembayaran
        $pembayaran = Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $total_pemakaian, // ← FIXED: Total pemakaian sekarang sudah ada
            'total_tagihan' => $request->total_bayar - $total_denda - $biaya_admin - $biaya_abodemen,
            'biaya_admin' => $biaya_admin,
            'biaya_abodemen' => $biaya_abodemen,
            'jumlah_dibayar' => $jumlah_dibayar,
            'tanggal_pembayaran' => now(),
            'denda' => $total_denda,
            'kembalian' => $kembalian,
        ]);

        \Log::info('Pembayaran berhasil disimpan', ['pembayaran' => $pembayaran]);
        \Log::info('Total tagihan:', ['total_tagihan' => $request->total_tagihan]);
        \Log::info('Total denda:', ['total_denda' => $total_denda]);
        \Log::info('Biaya admin:', ['biaya_admin' => $biaya_admin]);
        \Log::info('Biaya abodemen:', ['biaya_abodemen' => $biaya_abodemen]);
        \Log::info('Total pembayaran yang dihitung:', ['total_pembayaran' => $total_pembayaran]);

        
        // Update status pemakaian yang sudah dibayar
        foreach ($pemakaians as $pemakaian) {
            $pemakaian->id = $pembayaran->id;
            $pemakaian->save();
        }
        
        // Update tanggal pembayaran terakhir pelanggan
        $pelanggan->tanggal_pembayaran_terakhir = now();
        $pelanggan->save();
        \Log::info('Tanggal pembayaran pelanggan diperbarui');

        return redirect()->route('invoice.list')->with('success', 'Pembayaran berhasil! Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'));
    } catch (\Exception $e) {
        \Log::error('Terjadi error saat menyimpan pembayaran', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
    }
}



public function prosesBayar(Request $request)
{
    $pelanggans = Pelanggan::all();
    $nomorPelanggan = $request->input('nomor_pelanggan');
    
    // If a customer is selected, get their billing details
    if ($nomorPelanggan) {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomorPelanggan)->firstOrFail();
        
        // Get tariff configuration
        $tarif = $this->getTarif(); // Pastikan ini mengembalikan JSON
        $tarifData = json_decode($tarif, true); // Konversi JSON ke array
        $kategori = $pelanggan->kategori ?? 'R1'; // Default ke R1 jika tidak ada kategori

        // Calculate usage and bill
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
        // Ambil tarif yang sesuai dengan kategori pelanggan
        $tarif_per_kwh = isset($tarifData[$kategori]) ? floatval($tarifData[$kategori]) : 0;

        $totalTagihan = floatval($total_pemakaian) * $tarif_per_kwh;

        $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $nomorPelanggan)
    ->latest('tanggal_pembayaran')
    ->first();

        

        
        // Calculate late fee
        $total_denda = abs($request->denda ?? Pembayaran::hitungDenda($request->nomor_pelanggan));

        // Admin fee and subscription fee
        $biaya_admin = $request->biaya_admin ?? 2500;
        $biaya_abodemen = $request->biaya_abodemen ?? 0;
        
        // Calculate total payment
        $total_pembayaran = $totalTagihan + $total_denda + $biaya_admin + $biaya_abodemen;

        
        return view('invoice.pembayaran', compact(
            'pelanggans', 'nomorPelanggan'
        ))->with([
            'pelanggan' => $pelanggan ?? null,
            'totalTagihan' => $totalTagihan ?? 0,
            'total_denda' => $total_denda ?? 0,
            'biaya_admin' => $biaya_admin ?? 2500,
            'biaya_abodemen' => $biaya_abodemen ?? 0,
            'total_pembayaran' => $total_pembayaran ?? 0,
            'total_pemakaian' => $total_pemakaian ?? 0,
            'pembayaranTerakhir' => $pembayaranTerakhir ?? null,
        ]);
        
        
    }
    
    
    // If no customer is selected yet
    return view('invoice.pembayaran', compact('pelanggans'));
}

    
    public function getDenda($nomor_pelanggan)
    {
        $total_denda = Pembayaran::hitungDenda($nomor_pelanggan);
        return response()->json(['denda' => $total_denda]);
    }

    public function search(Request $request)
{
    $request->validate([
        'periode' => 'required|date_format:Y-m',
        'pelanggan_id' => 'required|exists:pelanggans,id',
    ]);

    $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
    $periode = $request->periode;

    // Ambil pembayaran terakhir pelanggan berdasarkan periode
    $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $pelanggan->nomor_pelanggan)
        ->whereYear('created_at', substr($periode, 0, 4))
        ->whereMonth('created_at', substr($periode, 5, 2))
        ->latest()
        ->first();

    $konfigurasi = Konfigurasi::first();
    $tarif_per_kwh = $konfigurasi->tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;

    // Hitung total pemakaian listrik
    $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
    $total_tagihan = $total_pemakaian * $tarif_per_kwh;

    // Pastikan denda selalu positif
    $total_denda = abs(Pembayaran::hitungDenda($pelanggan->nomor_pelanggan));

    // Hitung total biaya
    $total_biaya = $total_tagihan + $total_denda + ($pembayaranTerakhir->biaya_admin ?? 2500) + ($pembayaranTerakhir->biaya_abodemen ?? 0);

    return view('invoice.pembayaran', compact(
        'pelanggan',
        'periode',
        'pembayaranTerakhir',
        'total_pemakaian',
        'total_tagihan',
        'total_denda',
        'total_biaya'
    ));
}

public function getTagihan($nomor_pelanggan)
{
           // Validasi input
           $request->validate([
            'nomor_pelanggan' => 'required|exists:pelanggans,nomor_pelanggan',
            'total_pemakaian' => 'required|numeric|min:0',
            'total_tagihan' => 'required|numeric|min:0',
            'biaya_admin' => 'nullable|numeric|min:0', // Bisa kosong, default 2500
            'biaya_abodemen' => 'nullable|numeric|min:0', // Bisa kosong
            'denda' => 'nullable|numeric',
        ]);

        if (!$request->has('denda')) {
            $validated['denda'] = Pembayaran::hitungDenda($request->nomor_pelanggan);
        }
    
        // Ambil pelanggan
        $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();
    
        $total_denda = abs($request->denda ?? Pembayaran::hitungDenda($request->nomor_pelanggan));
    
        // Log the denda calculation
        \Log::info("Creating invoice for customer: " . $request->nomor_pelanggan);
        \Log::info("Calculated denda: " . $total_denda);
    
        // Ambil biaya admin, default ke 2500 jika tidak diisi
        $biaya_admin = $request->biaya_admin ?? 2500;
        $biaya_abodemen = $request->biaya_abodemen ?? 0;
    
        // Hitung total pembayaran
        $total_pembayaran = $request->total_tagihan + $total_denda + $biaya_admin + $biaya_abodemen;
    
        // Simpan pembayaran baru
        $pembayaran = Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'biaya_admin' => $biaya_admin,
            'biaya_abodemen' => $biaya_abodemen,
            'jumlah_dibayar' => 0,  // Belum dibayar
            'tanggal_pembayaran' => null, // Belum dibayar
            'denda' => $total_denda,  // Make sure this is saved
        ]);

        
        
        
        // Double-check the save was successful
        \Log::info("Saved invoice with ID: " . $pembayaran->id);
        \Log::info("Saved denda value: " . $pembayaran->denda);
        
        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dibuat.');
    
    // Double-check the save was successful
    \Log::info("Saved invoice with ID: " . $pembayaran->id);
    \Log::info("Saved denda value: " . $pembayaran->denda);
    
}


}