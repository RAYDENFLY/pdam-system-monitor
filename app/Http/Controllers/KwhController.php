<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kwh; // Pastikan model Kwh ada

class KwhController extends Controller
{
    public function create()
    {
        return view('kwh.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required',
            'kwh_bulan_ini' => 'required|numeric',
            'tanggal_catat' => 'required|date',
        ]);

        Kwh::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Data KWH berhasil disimpan.');
    }
}
