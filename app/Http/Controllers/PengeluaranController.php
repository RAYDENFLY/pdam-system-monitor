<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;


class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluarans = Pengeluaran::orderBy('tanggal', 'desc')->get();
        return view('pengeluaran.index', compact('pengeluarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Pengeluaran::create($request->all());

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }
    public function destroy($id)
{
    $pengeluaran = Pengeluaran::findOrFail($id);
    $pengeluaran->delete();

    return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');

}
public function edit($id)
{
    $pengeluaran = Pengeluaran::findOrFail($id);
    return view('pengeluaran.edit', compact('pengeluaran'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'tanggal' => 'required|date',
        'jumlah' => 'required|string',
        'keterangan' => 'nullable|string|max:255',
    ]);

    $jumlahBersih = str_replace('.', '', $request->jumlah);

    $pengeluaran = Pengeluaran::findOrFail($id);
    $pengeluaran->update([
        'tanggal' => $request->tanggal,
        'jumlah' => $jumlahBersih,
        'keterangan' => $request->keterangan,
    ]);

    return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
}



}
