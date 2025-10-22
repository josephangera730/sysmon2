@extends('layouts.main')

@section('main')

    <h3>Jenis Perangkat</h3>

    <!-- Tombol trigger modal create -->
    <div>
        <button type="button" class="btn btn-dx mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            Tambah
        </button>

    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table" id="basic-datatables">
            <thead class="table" >
                <tr>
                    <th>No</th>
                    <th>Jenis Perangkat</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->jenisperangkat }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>
                            <!-- Tombol edit -->
                            <button type="button" class="btn btn-dx btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}">
                                Edit
                            </button>

                            <!-- Form hapus -->
                            <form action="{{ route('jenisperangkat.destroy', $item->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-dx btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('jenisperangkat.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jenis Perangkat</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="jenisperangkat{{ $item->id }}" class="form-label">Jenis Perangkat</label>
                                            <input type="text" name="jenisperangkat" id="jenisperangkat{{ $item->id }}"
                                                value="{{ $item->jenisperangkat }}" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan{{ $item->id }}" class="form-label">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan{{ $item->id }}" class="form-control">{{ $item->keterangan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-dx">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jenisperangkat.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Perangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenisperangkat" class="form-label">Jenis Perangkat</label>
                        <input type="text" name="jenisperangkat" id="jenisperangkat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dx">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
