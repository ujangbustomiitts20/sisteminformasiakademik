<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        .input-group-text {
            background: #f8fafc;
            border-right: none;
        }
        .form-control.with-icon {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-mortarboard-fill"></i>
            <h4 class="mb-0">SIAKAD</h4>
            <small class="text-white-50">Sistem Informasi Akademik</small>
        </div>
        <div class="login-body">
            @if($errors->any())
            <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control with-icon" placeholder="Masukkan email" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control with-icon" placeholder="Masukkan password" required>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
                
                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="text-decoration-none small">
                        <i class="bi bi-question-circle me-1"></i>Lupa Password?
                    </a>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    &copy; {{ date('Y') }} SIAKAD - Universitas
                </small>
            </div>
        </div>
    </div>
</body>
</html>
