<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Abodemen;

class AbodemenController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'nomor_pelanggan' => 'required|exists:pelanggans,nomor_pelanggan',
            'abodemen' => 'required|integer|min:0',
        ]);

        Abodemen::updateOrCreate(
            ['nomor_pelanggan' => $request->nomor_pelanggan],
            ['abodemen' => $request->abodemen]
        );

        return redirect()->back()->with('success', 'Abodemen berhasil diperbarui.');
    }
}
