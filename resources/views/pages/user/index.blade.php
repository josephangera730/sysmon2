@extends('layouts.main')

@section('main')
<h4>Data Admin</h4>

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

<!-- Tombol Tambah User -->
@can('isSuperadmin')
    <div class="mb-3">
        <button class="btn btn-dx" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Tambah
        </button>
    </div>
@endcan

<div class="table-responsive mt-3">
    <table class="table" id="basic-datatables" >
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                @can('isSuperadmin')
                    <th>Aksi</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->email }}</td>
                    @can('isSuperadmin')
                        <td>
                            <button class="btn btn-sm btn-dx" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editUserModal{{ $item->id }}"
                                title="Ubah Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('user.destroy', $item->id) }}" method="POST" class="d-inline" title="Hapus Data">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dx" 
                                    onclick="return confirm('Yakin hapus data ini?')">
                                   <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    @endcan
                </tr>

                <!-- Modal Edit User -->
                <div class="modal fade" id="editUserModal{{ $item->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('user.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserModalLabel{{ $item->id }}">Edit Data Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" value="{{ $item->username }}" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" value="{{ $item->email }}" class="form-control" required>
                                    </div>
                                     <input type="hidden" name="role" value="Admin">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-dx">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Create User -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <input type="hidden" name="role" value="Admin">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dx">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
