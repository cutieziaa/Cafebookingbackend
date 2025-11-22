<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Login</title>
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
        
        .login-container {
            background-color: rgba(255, 248, 220, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .login-left {
            background: linear-gradient(rgba(139, 69, 19, 0.8), rgba(139, 69, 19, 0.9)), 
                        url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right {
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
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.4);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .forgot-password {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: var(--primary-color);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider-text {
            padding: 0 15px;
            color: var(--dark-color);
            font-size: 0.9rem;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .google {
            background-color: #db4437;
        }
        
        .twitter {
            background-color: #1da1f2;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color);
        }
        
        .register-link a {
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
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-lg-6 login-left">
                    <h1 class="display-4 fw-bold">Brew & Bites Cafe</h1>
                    <p class="lead">Where every cup tells a story</p>
                    <div class="cafe-features mt-4">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div>
                                <h5>Artisan Coffee</h5>
                                <p>Handcrafted by our expert baristas</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <div>
                                <h5>Free WiFi</h5>
                                <p>Stay connected while you enjoy</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-mug-hot"></i>
                            </div>
                            <div>
                                <h5>Cozy Ambiance</h5>
                                <p>Perfect place to work or relax</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 login-right">
                    <div class="cafe-logo">
                        <i class="fas fa-mug-hot"></i>
                        <span>Brew & Bites</span>
                    </div>
                    <h2 class="welcome-text">Welcome Back</h2>
                    <p class="subtitle">Sign in to your account to continue</p>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                                <span class="input-group-text"><i class="fas fa-eye" id="togglePassword"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </button>
                        
                        <div class="text-center mt-3">
                            @if (Route::has('password.request'))
                                <a class="forgot-password" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            @endif
                        </div>
                    </form>
                    
                    <div class="divider">
                        <span class="divider-text">Or continue with</span>
                    </div>
                    
                    <div class="social-login">
                        <a href="#" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-btn google">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                    
                    <div class="register-link">
                        Don't have an account? <a href="{{ route('register') }}">Sign up here</a>
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
    </script>
</body>
</html>