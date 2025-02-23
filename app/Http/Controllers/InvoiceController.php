<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Konfigurasi;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.index', compact('pelanggans'));
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
        $total_denda = Pembayaran::hitungDenda($nomor_pelanggan);

        
        
        // Hitung total pembayaran
        $totalPembayaran = $total_tagihan + $total_denda;
    
        return view('invoice.show', compact(
            'pelanggan',
            'pembayaranTerakhir',
            'total_pemakaian',
            'total_tagihan',
            'total_denda',
            'totalPembayaran'
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

            $sudahTerbayar = $pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= ($total_tagihan + $total_denda);

            if (!$sudahTerbayar) {
                $invoices[] = [
                    'pelanggan' => $pelanggan,
                    'total_pemakaian' => $total_pemakaian,
                    'total_tagihan' => $total_tagihan,
                    'total_denda' => $total_denda,
                    'pembayaran' => $pembayaranTerakhir
                ];
            }
        }

        return view('invoice.automatic', compact('invoices'));
    }

    public function updateStatus($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Update jumlah dibayar (lunas)
        $totalPembayaran = $pembayaran->total_tagihan + $pembayaran->denda;
        $pembayaran->jumlah_dibayar = $totalPembayaran;
        $pembayaran->tanggal_pembayaran = now();
        $pembayaran->save();
    
        // Update pelanggan
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
            $query->where('nama', 'like', "%$search%")
                ->orWhere('nomor_pelanggan', 'like', "%$search%");
        }

        $pelanggans = $query->get()->map(function ($pelanggan) {
            $pembayaranTerakhir = $pelanggan->pembayarans->first();
            $pelanggan->status_pembayaran = ($pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $pembayaranTerakhir->total_tagihan)
                ? 'lunas'
                : 'belum lunas';

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
        $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();

        // Ambil denda yang benar dari fungsi yang sudah diperbaiki
        $total_denda = Pembayaran::hitungDenda($request->nomor_pelanggan);

        // Simpan invoice baru dengan denda yang benar
        Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'jumlah_dibayar' => 0,
            'tanggal_pembayaran' => now(),
            'denda' => $total_denda,
        ]);

        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dibuat.');
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
        return view('invoice.list', compact('invoices'));
    }

    public function getDenda($nomor_pelanggan)
    {
        $total_denda = Pembayaran::hitungDenda($nomor_pelanggan);
        return response()->json(['denda' => $total_denda]);
    }
}


