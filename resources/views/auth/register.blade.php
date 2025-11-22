<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Brew & Bites Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D2691E;
            --accent-color: #F4A460;
            --light-color: #FFF8DC;
            --dark-color: #5D4037;
            --text-color: #3E2723;
        }
        
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            background-color: rgba(255, 248, 220, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .register-left {
            background: linear-gradient(rgba(139, 69, 19, 0.8), rgba(139, 69, 19, 0.9)), 
                        url('https://images.unsplash.com/photo-1493857671505-72967e2e2760?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-right {
            padding: 40px;
        }
        
        .cafe-logo {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .cafe-logo i {
            color: var(--secondary-color);
        }
        
        .welcome-text {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .subtitle {
            color: var(--dark-color);
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(210, 105, 30, 0.25);
        }
        
        .input-group-text {
            background-color: var(--light-color);
            border: 1px solid #ddd;
            border-right: none;
        }
        
        .btn-register {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.4);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color);
        }
        
        .login-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .cafe-features {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .feature-icon {
            background-color: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: -15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .strength-weak {
            background-color: #ff4757;
            width: 25%;
        }
        
        .strength-medium {
            background-color: #ffa502;
            width: 50%;
        }
        
        .strength-strong {
            background-color: #2ed573;
            width: 100%;
        }
        
        .terms-check {
            margin-top: 15px;
        }
        
        @media (max-width: 768px) {
            .register-left {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="row g-0">
                <div class="col-lg-6 register-left">
                    <h1 class="display-4 fw-bold">Join Our Cafe Community</h1>
                    <p class="lead">Become part of our story</p>
                    <div class="cafe-features mt-4">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div>
                                <h5>Member Discounts</h5>
                                <p>Enjoy exclusive offers and promotions</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5>Priority Booking</h5>
                                <p>Reserve your favorite spot in advance</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div>
                                <h5>Rewards Program</h5>
                                <p>Earn points with every purchase</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 register-right">
                    <div class="cafe-logo">
                        <i class="fas fa-mug-hot"></i>
                        <span>Brew & Bites</span>
                    </div>
                    <h2 class="welcome-text">Create Account</h2>
                    <p class="subtitle">Join our cafe community today</p>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required autocomplete="name" autofocus placeholder="Masukkan nama lengkap">
                            </div>
                            @error('nama')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Masukkan alamat email">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="nomor_wa" class="form-label">Nomor WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                <input id="nomor_wa" type="text" class="form-control @error('nomor_wa') is-invalid @enderror" name="nomor_wa" value="{{ old('nomor_wa') }}" required placeholder="Contoh: 08123456789">
                            </div>
                            @error('nomor_wa')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Buat password yang kuat">
                                <span class="input-group-text"><i class="fas fa-eye" id="togglePassword"></i></span>
                            </div>
                            <div id="password-strength" class="password-strength"></div>
                            @error('password')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password Anda">
                                <span class="input-group-text"><i class="fas fa-eye" id="toggleConfirmPassword"></i></span>
                            </div>
                            <div id="password-match" class="mt-2"></div>
                        </div>
                        
                        <div class="mb-3 terms-check">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Saya menyetujui <a href="#" class="text-decoration-none">Syarat & Ketentuan</a> dan <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-register">
                            <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                        </button>
                    </form>
                    
                    <div class="login-link">
                        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('password-confirm');
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength');
            
            // Reset
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) {
                return;
            }
            
            // Calculate strength
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 1;
            
            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 1;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Contains numbers
            if (/[0-9]/.test(password)) strength += 1;
            
            // Contains special characters
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update strength bar
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Password match indicator
        document.getElementById('password-confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchIndicator = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchIndicator.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchIndicator.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Password cocok</span>';
            } else {
                matchIndicator.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Password tidak cocok</span>';
            }
        });
    </script>
</body>
</html>