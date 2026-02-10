@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <div class="login-card">
        <!-- Logo/Image Section -->
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('img/cnhs.png') }}" alt="CNHS Logo" class="school-logo">
                <!-- Fallback if image doesn't exist - shows icon -->
                <div class="logo-fallback">
                    <i class="bi bi-building"></i>
                </div>
            </div>
            <h2 class="login-title">CNHS Smart Attendance Management System</h2>
            <p class="login-subtitle">Welcome back! Please login to continue</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.submit') }}" class="login-form">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-2"></i>
                    Email Address
                </label>
                <div class="input-wrapper">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        class="form-input @error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                        required 
                        autofocus>
                    <span class="input-icon">
                        <i class="bi bi-person-circle"></i>
                    </span>
                </div>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-2"></i>
                    Password
                </label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        required>
                    <span class="input-icon">
                        <i class="bi bi-shield-lock"></i>
                    </span>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="form-options">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="forgot-password">Forgot password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-login">
                <span class="btn-text">Login</span>
                <i class="bi bi-arrow-right-circle ms-2"></i>
            </button>
        </form>

        <!-- Footer -->
        <div class="login-footer">
            <p class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Secure Login • CNHS {{ date('Y') }}
            </p>
        </div>
    </div>
</div>

<style>
    /* Reset and Base Styles */
/* Apply background to the whole page */
body {
    margin: 0;
    padding: 0;
    box-sizing: border-box;

    /* Background image */
    background-image: url("https://scontent.fceb1-1.fna.fbcdn.net/v/t39.30808-6/508134764_1175545101038112_8691161198505834852_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeHPf910jdXRmUzcGUuRjSpMIEdPGcCx_VQgR08ZwLH9VNvB_dcx7swaKSTRqXpvSN-IPcoA4YS-KGOCTvI4Yf6y&_nc_ohc=-YWRce_sB7MQ7kNvwGTDVAU&_nc_oc=AdkIfVYImLnS18H9onT7tlgZ0vigLmhE86CD_zaswgixkJ-A8w8iWJlhNOzNY-R_B5eduCJrxCRAszewecVmgQ0g&_nc_zt=23&_nc_ht=scontent.fceb1-1.fna&_nc_gid=R0MMa_sKtNBe3u2bD3aPnQ&oh=00_AfsdSR-Pk7H6wSDzeW78ONWMc9VY0YYY_njrRka7Bdo8XA&oe=698718A4");

    background-size: cover;      /* Make it cover the whole page */
    background-repeat: no-repeat; /* Don’t repeat */
    background-position: center;  /* Center it */
    height: 100vh;               /* Make body take full viewport height */
}


    .login-container {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    /* Animated Background */
    .login-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .bg-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .shape-1 {
        width: 400px;
        height: 400px;
        top: -100px;
        right: -100px;
        animation: float 20s infinite ease-in-out;
    }

    .shape-2 {
        width: 300px;
        height: 300px;
        bottom: -50px;
        left: -50px;
        animation: float 15s infinite ease-in-out reverse;
    }

    .shape-3 {
        width: 200px;
        height: 200px;
        top: 50%;
        left: 10%;
        animation: float 18s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    /* Login Card */
    .login-card {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        width: 90%;
        max-width: 480px;
        position: relative;
        /* z-index: 1; */
        animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Logo Section */
    .login-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .logo-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem;
    }

    .school-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        padding: 1rem;
        display: block;
    }

    /* Logo Fallback (shows if image doesn't load) */
    .logo-fallback {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
        z-index: -1;
    }

    .logo-fallback i {
        font-size: 4rem;
        color: white;
    }

    /* Hide fallback when image loads */
    .school-logo:not([src=""]) ~ .logo-fallback {
        display: none;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .login-subtitle {
        color: #6b7280;
        font-size: 0.9375rem;
    }

    /* Alert Error */
    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        color: #dc2626;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        font-weight: 500;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    /* Form Styles */
    .login-form {
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
    }

    .form-label i {
        color: #3b82f6;
    }

    .input-wrapper {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 1rem 3rem 1rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-input.is-invalid {
        border-color: #ef4444;
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .input-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .form-input:focus ~ .input-icon {
        color: #3b82f6;
    }

    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }

    .toggle-password:hover {
        color: #3b82f6;
    }

    .toggle-password i {
        font-size: 1.125rem;
    }

    .error-message {
        display: block;
        color: #ef4444;
        font-size: 0.8125rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    /* Form Options */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .form-check-label {
        color: #6b7280;
        font-size: 0.875rem;
        cursor: pointer;
        user-select: none;
    }

    .forgot-password {
        color: #3b82f6;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .forgot-password:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    /* Login Button */
    .btn-login {
        width: 100%;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.0625rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* Footer */
    .login-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 2px solid #f3f4f6;
    }

    .text-muted {
        color: #9ca3af;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    /* Responsive Design */
    @media (max-width: 640px) {
        .login-card {
            padding: 2rem 1.5rem;
        }

        .logo-container {
            width: 100px;
            height: 100px;
        }

        .login-title {
            font-size: 1.5rem;
        }

        .form-options {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
    }

    /* Loading State */
    .btn-login.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-login.loading .btn-text::after {
        content: '...';
        animation: dots 1.5s infinite;
    }

    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60%, 100% { content: '...'; }
    }
</style>

@if(session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
@endif


<script>
    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });

    // Add loading state to button on submit
    document.querySelector('.login-form').addEventListener('submit', function() {
        const btn = document.querySelector('.btn-login');
        btn.classList.add('loading');
        btn.querySelector('.btn-text').textContent = 'Logging in';
    });

    // Auto-hide error message after 5 seconds
    const errorAlert = document.querySelector('.alert-error');
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.animation = 'fadeOut 0.5s ease';
            setTimeout(() => errorAlert.remove(), 500);
        }, 5000);
    }

    // Add fadeOut animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection