<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Jaringan Internet OPD Kota Pariaman</title>
    <link
      rel="icon"
      href="{{ asset('logo-sidebar.png') }}"
      type="image/x-icon"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f3f3f3, #ffffff);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        }

        .login-card h4, .login-card h5 {
            font-weight: 600;
            color: #2a2a2a;
        }

        .login-card .form-label {
            font-weight: 500;
            color: #444;
        }

        .login-card .form-control {
            border-radius: 12px;
            padding: 10px;
            border: 1px solid #ddd;
            transition: 0.3s;
        }

        .login-card .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 8px rgba(42, 82, 152, 0.3);
        }

        .login-card .btn {
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            background: #2a5298;
            border: none;
            transition: 0.3s;
        }

        .login-card .btn:hover {
            background: #1e3c72;
            transform: translateY(-2px);
        }

       
    </style>
</head>
<body>

    <div class="login-card">
       

        {{-- Pesan Error --}}
        @if(session('error') || session('failed'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <strong>Gagal!</strong> {{ session('error') ?? session('failed') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Pesan Sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Form Kirim OTP --}}
        <form method="POST" action="{{ route('password.sendOtp') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Masukkan Email Anda</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-grid mt-4 mb-2">
                <button type="submit" class="btn btn-primary">Kirim OTP</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none">
                Kembali ke Login
            </a>
        </div>
    </div>

    <!-- Modal Input OTP -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="otpModalLabel">Verifikasi OTP & Atur Password Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('password.verify') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">

                <!-- OTP -->
                <div class="mb-3">
                    <label for="otp" class="form-label">Kode OTP</label>
                    <input 
                        type="text" 
                        name="otp" 
                        id="otp" 
                        class="form-control @error('otp') is-invalid @enderror" 
                        required 
                        maxlength="6"
                    >
                    @error('otp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        required
                        minlength="6"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        required
                        minlength="6"
                    >
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success">Verifikasi & Simpan</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    </div>


    {{-- Script untuk auto-buka modal kalau ada session OTP --}}
    @if(session('OTP'))
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            let otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
        });
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
