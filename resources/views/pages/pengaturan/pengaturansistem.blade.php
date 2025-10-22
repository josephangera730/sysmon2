@extends('layouts.main')

@section('main')
<div class="container mt-4">
    <h4 class="mb-4 fw-bold">Pengaturan Sistem</h4>
    {{-- Alert Session (Success, Failed, Error Validation) --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@elseif(session('failed') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-start" role="alert">
        <i class="fas fa-times-circle me-2 mt-1"></i>
        <div>
            {{-- Jika ada session failed --}}
            @if(session('failed'))
                <div>{{ session('failed') }}</div>
            @endif

            {{-- Jika ada error validasi --}}
            @if($errors->any())
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


    {{-- Card Form Update --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-envelope me-2"></i> Pengaturan Email
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('pengaturansistem.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Email Penerima -->
                <div class="mb-4">
                    <label for="emailpenerima" class="form-label fw-semibold">Email Penerima</label>
                    <small class="text-muted d-block mb-2">Email ini digunakan sebagai penerima laporan internet dari berbagai OPD.</small>
                    <input type="email" name="emailpenerima" id="emailpenerima" 
                        class="form-control shadow-sm rounded-3 @error('emailpenerima') is-invalid @enderror" 
                        placeholder="contoh@email.com"
                        value="{{ old('emailpenerima', $emailpenerima) }}">
                    @error('emailpenerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Sistem -->
                <div class="mb-4">
                    <label for="emailsistem" class="form-label fw-semibold">Email Sistem</label>
                    <small class="text-muted d-block mb-2">Email ini digunakan sistem untuk mengirim laporan dan kode reset password.</small>
                    <input type="email" name="emailsistem" id="emailsistem" 
                        class="form-control shadow-sm rounded-3 @error('emailsistem') is-invalid @enderror" 
                        placeholder="sistem@email.com"
                        value="{{ old('emailsistem', $emailsistem) }}">
                    @error('emailsistem')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sandi Aplikasi Email -->
                <div class="mb-4">
                    <label for="sandiaplikasiemail" class="form-label fw-semibold">Sandi Aplikasi Email</label>
                    <small class="text-muted d-block mb-2">Masukkan sandi aplikasi email untuk otentikasi SMTP.</small>
                    <input type="password" name="sandiaplikasiemail" id="sandiaplikasiemail" 
                        class="form-control shadow-sm rounded-3 @error('sandiaplikasiemail') is-invalid @enderror" 
                        placeholder="********"
                        value="{{ old('sandiaplikasiemail', $sandiaplikasiemail) }}">
                    @error('sandiaplikasiemail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-dx px-4">
                        <i class="fas fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Card Reset Sistem --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body bg-light border-start border-5 border-danger rounded p-4">
            <h5 class="text-danger fw-bold mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i> Reset Sistem
            </h5>
            <p class="mb-3 text-muted">
                Melakukan reset sistem akan menghapus:
            </p>
            <ul class="text-muted mb-4">
                <li>Seluruh <b>data pengguna</b></li>
                <li>Semua <b>data perangkat & internet</b></li>
                <li>Seluruh <b>laporan jaringan</b></li>
            </ul>
            <p class="text-danger fw-bold mb-4">
                ⚠️ Tindakan ini <u>tidak dapat dibatalkan</u>. Lakukan hanya jika benar-benar diperlukan!
            </p>
            <div class="text-end">
                <button type="button" class="btn btn-lg btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#resetModal">
                    <i class="fas fa-trash-alt me-2"></i> Reset Sistem Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Reset -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="resetModalLabel">
                <i class="fas fa-exclamation-circle me-2"></i> Konfirmasi Reset Sistem
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center p-4">
            <div class="mb-3">
                <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
            </div>
            <h5 class="fw-bold mb-3 text-danger">Apakah Anda Yakin?</h5>
            <p class="mb-2">Seluruh data sistem akan <b class="text-danger">dihapus permanen</b>.</p>
            <p class="text-muted small">Tindakan ini tidak bisa dibatalkan.</p>
          </div>
          <div class="modal-footer justify-content-center">
            <form action="{{ route('pengaturansistem.reset') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-dx px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" class="btn btn-danger px-4">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus Semua Data
                </button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
