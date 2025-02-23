@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Input KWH</h2>

    <form action="{{ route('kwh.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">ID Pelanggan</label>
            <input type="text" name="id_pelanggan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">KWH Bulan Ini</label>
            <input type="number" name="kwh_bulan_ini" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Catat</label>
            <input type="date" name="tanggal_catat" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
