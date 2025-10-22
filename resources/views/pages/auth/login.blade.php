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

        .login-card h4 {
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

       

        .

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center">
            <h3>Login</h3>
        </div>

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


       <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
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

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="form-control @error('password') is-invalid @enderror" 
                required
            >
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Lupa Password -->
        <div class="text-start mt-3">
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                Lupa Password?
            </a>
        </div>
        <!-- Tombol Login -->
        <div class="d-grid mt-3 mb-2">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>


    </form>

        <div class="text-center mt-4">
            <small class="text-muted">© 2025 Pariaman Kota</small>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
