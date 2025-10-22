@extends('layouts.main')

@section('main')
<h4>Laporan Internet</h4>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('failed'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('failed') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@can('isSuperadmin')
    
<div>
    <a href="/clearlaporan" 
        class="btn btn-dx" 
        onclick="return confirm('Apakah Anda yakin ingin membersihkan data laporan ?')">
        Bersihkan
    </a>
</div>
@endcan
<div class="table-responsive mt-3">
    <table class="table">
        <thead>
            <tr>
                <th>Pengirim</th>
                <th>Laporan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ $item->pengirim }}</td>
                    <td>{{ $item->laporan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada log</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="text-end">
    {{ $data->links() }}

</div>

@endsection
