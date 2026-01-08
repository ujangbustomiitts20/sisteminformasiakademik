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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f2f5;
        }
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0061f2 0%, #6900c7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -150px;
            left: -150px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
        }
        .login-left-content {
            position: relative;
            z-index: 1;
            color: #fff;
            text-align: center;
            max-width: 450px;
        }
        .login-left-content .logo-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(10px);
        }
        .login-left-content .logo-icon i {
            font-size: 3rem;
        }
        .login-left-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .login-left-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.7;
        }
        .feature-list {
            margin-top: 2.5rem;
            text-align: left;
        }
        .feature-list .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
        }
        .feature-list .feature-item i {
            font-size: 1.25rem;
            margin-right: 1rem;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-right {
            width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: #fff;
        }
        .login-form-container {
            width: 100%;
            max-width: 380px;
        }
        .login-form-container h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .login-form-container .subtitle {
            color: #64748b;
            margin-bottom: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .form-floating > .form-control {
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            padding: 1rem 1rem 1rem 3rem;
            height: 60px;
            font-size: 1rem;
        }
        .form-floating > .form-control:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 4px rgba(0, 97, 242, 0.1);
        }
        .form-floating > label {
            padding: 1rem 1rem 1rem 3rem;
            color: #94a3b8;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
            font-size: 1.25rem;
        }
        .form-floating:focus-within .input-icon {
            color: #0061f2;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            font-size: 1.25rem;
        }
        .password-toggle:hover {
            color: #0061f2;
        }
        .btn-login {
            background: linear-gradient(135deg, #0061f2 0%, #6900c7 100%);
            border: none;
            padding: 1rem;
            font-weight: 600;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 97, 242, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #94a3b8;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .divider span {
            padding: 0 1rem;
            font-size: 0.875rem;
        }
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem;
        }
        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
        }
        .form-check-input:checked {
            background-color: #0061f2;
            border-color: #0061f2;
        }
        @media (max-width: 992px) {
            .login-left {
                display: none;
            }
            .login-right {
                width: 100%;
            }
            body {
                background: linear-gradient(135deg, #0061f2 0%, #6900c7 100%);
            }
            .login-right {
                border-radius: 1.5rem;
                margin: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="login-left-content">
            <div class="logo-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1>NADI ITTS</h1>
            <p>Narasi & Akademik Data Integratif Institut Teknologi Tangerang Selatan</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Keamanan data terjamin</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Akses cepat & responsif</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Monitoring real-time</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="login-right">
        <div class="login-form-container">
            <h2>Selamat Datang!</h2>
            <p class="subtitle">Silakan masuk ke akun Anda</p>
            
            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
            </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-floating position-relative mb-3">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <label for="email">Email</label>
                </div>
                
                <div class="form-floating position-relative mb-3">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">
                        Lupa Password?
                    </a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    &copy; {{ date('Y') }} NADI ITTS - {{ setting('nama_institusi', 'Universitas') }}
                </small>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
