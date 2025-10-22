@extends('layouts.main')

@section('main')
<h4>Log Perangkat</h4>

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
    <a href="/clearlog" 
        class="btn btn-dx" 
        onclick="return confirm('Apakah Anda yakin ingin membersihkan log?')">
        Bersihkan
    </a>
</div>

@endcan
<div class="table-responsive mt-3">
    <table class="table">
        <thead>
            <tr>
                <th>Pesan</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ $item->message }}</td>
                    <td>{{ $item->created_at }}</td>
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
