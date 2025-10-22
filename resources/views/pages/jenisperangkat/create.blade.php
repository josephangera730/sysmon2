@extends('layouts.template')

@section('main')
<div class="container mt-4">
    <h3>Tambah Jenis Perangkat</h3>

    <form action="{{ route('jenisperangkat.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="jenisperangkat" class="form-label">Jenis Perangkat</label>
            <input type="text" name="jenisperangkat" id="jenisperangkat" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('jenisperangkat.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
