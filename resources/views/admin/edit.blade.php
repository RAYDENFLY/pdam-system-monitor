@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Karyawan</h2>

    <form action="{{ route('admin.updateUser', $user->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.register') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
