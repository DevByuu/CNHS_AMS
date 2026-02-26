<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CNHS Smart Attendance Management System | Concepcion National High School</title>
    <link rel="icon" href="{{ asset('img/cnhs.png') }}" type="image/png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-blue-dark: #1e40af;
            --primary-blue-light: #3b82f6;
            --accent-blue: #0ea5e9;
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-900: #1e3a8a;
            --sidebar-width: 260px;
            --navbar-height: 68px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
        }

        html, body {
            min-height: 100%;
            height: auto;
            margin: 0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        body {
            background: #f8fafc;
            overflow-y: auto !important;
        }

        /* ════════════════════════════
           MODERN GUEST NAVBAR
        ════════════════════════════ */
        .guest-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            z-index: 1050;
            transition: all 0.3s ease;
        }

        .guest-navbar.scrolled {
            height: 70px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.12);
        }

        .guest-navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .guest-navbar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .guest-navbar-brand:hover {
            transform: translateY(-2px);
        }

        .guest-brand-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(37, 99, 235, 0.2));
        }

        .guest-brand-text {
            display: flex;
            flex-direction: column;
        }

        .guest-brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .guest-brand-subtitle {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
        }

        .guest-navbar-menu {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .guest-nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .guest-nav-link {
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        .guest-nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            transition: width 0.3s ease;
        }

        .guest-nav-link:hover {
            color: #2563eb;
        }

        .guest-nav-link:hover::after {
            width: 100%;
        }

        .guest-nav-link.active {
            color: #2563eb;
        }

        .guest-nav-link.active::after {
            width: 100%;
        }

        .guest-navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-guest-primary {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: white;
            padding: 0.625rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9375rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-guest-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .guest-mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #1e40af;
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Mobile Menu */
        .guest-mobile-menu {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            z-index: 1040;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .guest-mobile-menu.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .guest-mobile-menu .guest-nav-links {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .guest-mobile-menu .guest-nav-link {
            font-size: 1.125rem;
            padding: 0.75rem 0;
            width: 100%;
            border-bottom: 1px solid #e2e8f0;
        }

        .guest-mobile-menu .btn-guest-primary {
            width: 100%;
            justify-content: center;
        }

        /* ════════════════════════════
           SIDEBAR (Admin)
        ════════════════════════════ */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(175deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0; bottom: 0; left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1050;
            padding: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        .sidebar-brand {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.75rem 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.75rem;
            text-decoration: none;
        }

        .sidebar-brand img {
            width: 72px; height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }

        .sidebar-nav {
            padding: 0.5rem 0.875rem;
            flex: 1;
        }

        .nav-section-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.75rem 0.75rem 0.4rem;
        }

        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            text-decoration: none;
        }

        .nav-pills .nav-link .nav-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.08);
            font-size: 1.05rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }

        .nav-pills .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-pills .nav-link.active .nav-icon {
            background: var(--accent-blue);
            box-shadow: 0 4px 12px rgba(14,165,233,0.4);
        }

        .nav-pills .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--accent-blue);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer-text {
            color: rgba(255,255,255,0.35);
            font-size: 0.72rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        /* ════════════════════════════
           ADMIN TOP NAVBAR
        ════════════════════════════ */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: rgba(255,255,255,0.98);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 20px rgba(0,0,0,0.07);
            padding: 0 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1040;
            transition: left 0.3s ease;
        }

        .navbar-left { display: flex; align-items: center; gap: 0.75rem; flex: 1; }

        .sidebar-toggle-btn {
            width: 38px; height: 38px;
            border-radius: 9px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .navbar-title {
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-right { display: flex; align-items: center; gap: 0.75rem; }

        .user-badge {
            background: var(--blue-50);
            color: var(--primary-blue);
            padding: 0.45rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid var(--blue-100);
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 0.45rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(220,38,38,0.25);
        }

        /* ════════════════════════════
           MAIN CONTENT
        ════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            min-height: calc(100vh - var(--navbar-height));
            padding: 2rem;
            background: rgba(248, 250, 253, 0.95);
            transition: margin-left 0.3s ease;
        }

        .main-content.full-width {
            margin-left: 0;
            margin-top: 80px;
            background: transparent;
            padding: 0;
            min-height: calc(100vh - 80px);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1049;
            backdrop-filter: blur(2px);
        }

        /* ════════════════════════════
           GUEST LANDING SECTIONS
        ════════════════════════════ */

        /* --- Hero / Home Section --- */
        #home {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #1e40af 70%, #0369a1 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 120px 0 80px;
        }

        /* Animated background blobs */
        #home::before {
            content: '';
            position: absolute;
            top: -15%;
            right: -8%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(14,165,233,0.18) 0%, transparent 65%);
            pointer-events: none;
            animation: floatBlob 8s ease-in-out infinite;
        }

        #home::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 65%);
            pointer-events: none;
            animation: floatBlob 10s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(20px, -20px) scale(1.05); }
        }

        .hero-grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            color: #93c5fd;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.45rem 1.1rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(8px);
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            color: white;
            line-height: 1.15;
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
        }

        .hero-title .hero-highlight {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: #bfdbfe;
            line-height: 1.8;
            max-width: 520px;
            margin-bottom: 2.25rem;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 24px rgba(37,99,235,0.4);
            text-decoration: none;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(37,99,235,0.5);
            color: white;
        }

        .btn-hero-secondary {
            color: #bfdbfe;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: color 0.2s ease;
            padding: 0.875rem 0;
        }

        .btn-hero-secondary:hover {
            color: white;
        }

        .btn-hero-secondary i {
            font-size: 1.1rem;
        }

        /* Hero stats row */
        .hero-stats {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-wrap: wrap;
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
        }

        .hero-stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            line-height: 1;
        }

        .hero-stat-label {
            font-size: 0.75rem;
            color: #93c5fd;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .hero-stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255,255,255,0.15);
        }

        /* Hero right card */
        .hero-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            padding: 2rem;
            backdrop-filter: blur(16px);
            position: relative;
            z-index: 2;
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .hero-card-logo {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hero-card-logo img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .hero-card-school {
            flex: 1;
        }

        .hero-card-school-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: white;
            line-height: 1.3;
        }

        .hero-card-school-loc {
            font-size: 0.72rem;
            color: #93c5fd;
        }

        .hero-live-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: #4ade80;
            background: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.25);
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
        }

        .hero-live-dot {
            width: 7px;
            height: 7px;
            background: #4ade80;
            border-radius: 50%;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.7); }
        }

        .hero-feature-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .hero-feature-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            transition: all 0.25s ease;
        }

        .hero-feature-row:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.15);
        }

        .hero-feature-row-icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: rgba(14,165,233,0.2);
            border: 1px solid rgba(14,165,233,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .hero-feature-row-text h6 {
            font-size: 0.82rem;
            font-weight: 700;
            color: white;
            margin: 0 0 0.1rem;
        }

        .hero-feature-row-text p {
            font-size: 0.72rem;
            color: #93c5fd;
            margin: 0;
        }

        .hero-card-footer {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.3);
        }

        /* Scroll down hint */
        .hero-scroll-hint {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.3);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            z-index: 2;
            animation: bounce-hint 2s ease-in-out infinite;
        }

        @keyframes bounce-hint {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(6px); }
        }

        /* Features Section */
        #features {
            background: #f8fafc;
            padding: 100px 0 80px;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--blue-100);
            color: var(--primary-blue);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .section-title span {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 560px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            height: 100%;
            border: 1px solid #e2e8f0;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.12);
            border-color: transparent;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
        }

        .feature-icon-wrap.blue   { background: #dbeafe; color: #2563eb; }
        .feature-icon-wrap.sky    { background: #e0f2fe; color: #0284c7; }
        .feature-icon-wrap.indigo { background: #e0e7ff; color: #4338ca; }
        .feature-icon-wrap.teal   { background: #ccfbf1; color: #0d9488; }
        .feature-icon-wrap.violet { background: #ede9fe; color: #7c3aed; }
        .feature-icon-wrap.green  { background: #dcfce7; color: #16a34a; }

        .feature-card h5 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.65;
            margin: 0;
        }

        /* --- About Section --- */
        #about {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #0369a1 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        #about::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        #about::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .about-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            color: #bfdbfe;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 1rem;
        }

        .about-title {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            color: white;
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        .about-desc {
            color: #bfdbfe;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .about-stat-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .about-stat-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
        }

        .about-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .about-stat-label {
            font-size: 0.8rem;
            color: #93c5fd;
            font-weight: 500;
        }

        .about-visual {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }

        .about-visual-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .about-visual-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .about-visual-item:first-child {
            padding-top: 0;
        }

        .about-visual-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(14, 165, 233, 0.2);
            border: 1px solid rgba(14, 165, 233, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .about-visual-text h6 {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .about-visual-text p {
            color: #93c5fd;
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.5;
        }

        /* --- Contact Section --- */
        #contact {
            background: white;
            padding: 100px 0 80px;
        }

        .contact-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .contact-card:hover {
            background: white;
            border-color: #bfdbfe;
            box-shadow: 0 10px 40px rgba(37, 99, 235, 0.1);
            transform: translateY(-4px);
        }

        .contact-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .contact-card h5 {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .contact-card p {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .contact-card a {
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .contact-card a:hover {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        .contact-form-wrap {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 2.5rem;
        }

        .contact-form-wrap .form-control,
        .contact-form-wrap .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: white;
        }

        .contact-form-wrap .form-control:focus,
        .contact-form-wrap .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .contact-form-wrap .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .btn-contact-submit {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: white;
            border: none;
            padding: 0.85rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-contact-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        /* --- Developer Credits Section --- */
        #credits {
            background: #f1f5f9;
            padding: 80px 0 70px;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            z-index: 0;
        }

        #credits::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 50%, #6366f1 100%);
        }

        .credits-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--blue-100);
            color: var(--primary-blue);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1rem;
        }

        .dev-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 2.25rem;
            display: flex;
            align-items: center;
            gap: 1.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dev-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s ease;
        }

        .dev-card:hover {
            box-shadow: 0 16px 50px rgba(37, 99, 235, 0.1);
            transform: translateY(-4px);
            border-color: #bfdbfe;
        }

        .dev-card:hover::before {
            transform: scaleX(1);
        }

        .dev-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
            overflow: hidden;
            border: 3px solid white;
            outline: 3px solid #bfdbfe;
        }

        .dev-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dev-avatar-initials {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .dev-info { flex: 1; }

        .dev-role {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: var(--blue-50);
            color: var(--primary-blue);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            border: 1px solid var(--blue-100);
            margin-bottom: 0.5rem;
        }

        .dev-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }

        .dev-course {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .dev-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .dev-tag {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
        }

        .credits-note {
            text-align: center;
            margin-top: 2.5rem;
            padding: 1.5rem 2rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        .credits-note i {
            font-size: 1.25rem;
            color: var(--primary-blue);
        }

        /* ── DEVELOPER PROFILE MODAL ─────────────────────────── */
        .dev-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(8,15,35,0.78);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            padding: 1.25rem;
            opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .dev-modal-overlay.active { opacity: 1; visibility: visible; }

        .dev-modal {
            background: #0f172a;
            border-radius: 0;
            width: 100%; max-width: 700px;
            max-height: calc(100vh - 2.5rem);
            overflow: hidden;
            box-shadow: 0 32px 100px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.06);
            transform: translateY(24px) scale(0.98);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34,1.2,0.64,1), opacity 0.28s ease;
            display: flex; flex-direction: column;
            position: relative;
        }
        .dev-modal-overlay.active .dev-modal { transform: translateY(0) scale(1); opacity: 1; }

        /* Top accent */
        .dev-modal::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, #2563eb 0%, #0ea5e9 40%, #818cf8 100%);
            z-index: 10;
        }

        /* ── HEADER ── */
        .dev-modal-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.9rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        .dev-modal-topbar-label {
            font-size: 0.65rem; font-weight: 700;
            letter-spacing: 0.14em; text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            display: flex; align-items: center; gap: 0.5rem;
        }
        .dev-modal-topbar-label i { color: #38bdf8; font-size: 0.8rem; }
        .dev-modal-close-btn {
            width: 30px; height: 30px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 0.8rem;
            transition: all 0.2s ease; border-radius: 0; flex-shrink: 0;
        }
        .dev-modal-close-btn:hover { background: #dc2626; border-color: #dc2626; color: white; }

        /* ── HERO BAND ── */
        .dev-modal-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 55%, #0369a1 100%);
            padding: 2rem 2rem 0;
            display: flex; align-items: flex-end; gap: 1.75rem;
            position: relative; overflow: hidden; flex-shrink: 0;
        }
        .dev-modal-hero::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(14,165,233,0.18) 0%, transparent 65%);
            pointer-events: none;
        }
        .dev-modal-hero::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0; height: 32px;
            background: #0f172a;
            clip-path: ellipse(56% 100% at 50% 100%);
            pointer-events: none;
        }
        .dev-modal-hero-photo {
            width: 100px; height: 100px;
            border-radius: 0;
            border: 3px solid rgba(255,255,255,0.3);
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            flex-shrink: 0;
            position: relative; z-index: 1;
            margin-bottom: -1px;
        }
        .dev-modal-hero-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .dev-modal-hero-info {
            flex: 1; padding-bottom: 1.25rem; position: relative; z-index: 1;
        }
        .dev-modal-hero-name {
            font-size: 1.4rem; font-weight: 800; color: white;
            line-height: 1.2; margin-bottom: 0.2rem; letter-spacing: -0.01em;
        }
        .dev-modal-hero-title {
            font-size: 0.78rem; color: #93c5fd; font-weight: 600; margin-bottom: 0.75rem;
        }
        .dev-modal-hero-badges {
            display: flex; flex-wrap: wrap; gap: 0.4rem;
        }
        .dev-modal-hero-badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.25rem 0.7rem;
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.06em;
            border-radius: 0;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            color: #e0f2fe;
        }

        /* ── TABS ── */
        .dev-modal-tabs {
            display: flex; border-bottom: 1px solid rgba(255,255,255,0.08);
            background: #0f172a; flex-shrink: 0; padding: 0 1.5rem;
        }
        .dev-modal-tab {
            padding: 0.75rem 1.1rem;
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em;
            text-transform: uppercase; color: rgba(255,255,255,0.35);
            cursor: pointer; border: none; background: none;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease; display: flex; align-items: center; gap: 0.4rem;
        }
        .dev-modal-tab:hover { color: rgba(255,255,255,0.6); }
        .dev-modal-tab.active { color: #38bdf8; border-bottom-color: #38bdf8; }

        /* ── PANELS ── */
        .dev-modal-panels { overflow-y: auto; flex: 1; min-height: 0; }
        .dev-modal-panels::-webkit-scrollbar { width: 4px; }
        .dev-modal-panels::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        .dev-modal-panel { display: none; padding: 1.5rem; }
        .dev-modal-panel.active { display: block; }

        /* Info grid */
        .dev-info-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;
        }
        .dev-info-cell {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            padding: 0.85rem 1rem;
        }
        .dev-info-cell label {
            display: block; font-size: 0.6rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(255,255,255,0.3); margin-bottom: 0.3rem;
        }
        .dev-info-cell span { font-size: 0.82rem; font-weight: 600; color: #e2e8f0; }

        .dev-about-text {
            font-size: 0.85rem; color: rgba(255,255,255,0.55);
            line-height: 1.8; margin-bottom: 1.25rem;
        }

        .dev-stack-wrap { display: flex; flex-wrap: wrap; gap: 0.4rem; }
        .dev-stack-chip {
            background: rgba(37,99,235,0.15);
            border: 1px solid rgba(37,99,235,0.3);
            color: #93c5fd;
            font-size: 0.7rem; font-weight: 600;
            padding: 0.3rem 0.75rem; border-radius: 0;
            display: inline-flex; align-items: center; gap: 0.35rem;
            transition: all 0.2s;
        }
        .dev-stack-chip:hover {
            background: rgba(37,99,235,0.3);
            border-color: rgba(37,99,235,0.5);
            color: white;
        }

        /* System panel */
        .dev-system-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            padding: 1.1rem 1.25rem;
            margin-bottom: 0.75rem;
            display: flex; align-items: flex-start; gap: 1rem;
        }
        .dev-system-card-icon {
            width: 38px; height: 38px; flex-shrink: 0;
            background: rgba(14,165,233,0.15);
            border: 1px solid rgba(14,165,233,0.25);
            display: flex; align-items: center; justify-content: center;
            color: #38bdf8; font-size: 1rem;
        }
        .dev-system-card h6 {
            font-size: 0.8rem; font-weight: 700; color: #e2e8f0;
            margin-bottom: 0.3rem;
        }
        .dev-system-card p {
            font-size: 0.76rem; color: rgba(255,255,255,0.45);
            margin: 0; line-height: 1.6;
        }

        .dev-system-desc {
            font-size: 0.85rem; color: rgba(255,255,255,0.5);
            line-height: 1.8; margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.03);
            border-left: 2px solid #2563eb;
        }

        .dev-modal-section-head {
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: rgba(255,255,255,0.25);
            margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .dev-modal-section-head::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.06);
        }

        /* ── BOTTOM BAR ── */
        .dev-modal-bottombar {
            background: rgba(255,255,255,0.03);
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 0.7rem 1.5rem;
            font-size: 0.65rem; color: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .dev-modal-bottombar span { display: flex; align-items: center; gap: 0.4rem; }

        @media (max-width: 560px) {
            .dev-modal { max-width: 100%; }
            .dev-info-grid { grid-template-columns: 1fr; }
            .dev-modal-hero { flex-direction: column; align-items: flex-start; gap: 1rem; padding-bottom: 1.25rem; }
            .dev-modal-hero::after { display: none; }
            .dev-modal-hero-photo { margin-bottom: 0; }
        }

        /* --- Footer --- */
        .guest-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 2.5rem 0;
            text-align: center;
            font-size: 0.85rem;
        }

        .guest-footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        .guest-footer a:hover {
            color: #93c5fd;
        }

        /* Scroll Reveal Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }

        /* ════════════════════════════
           RESPONSIVE
        ════════════════════════════ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1060;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show { display: block; }
            .top-navbar { left: 0; }
            .sidebar-toggle-btn { display: flex; }
            .main-content { margin-left: 0; }

            .guest-navbar-menu {
                display: none;
            }
            .guest-mobile-toggle {
                display: block;
            }

            #features, #about, #contact, #credits {
                padding: 70px 0 60px;
            }
        }

        @media (max-width: 767.98px) {
            .guest-navbar-container {
                padding: 0 1rem;
            }
            .guest-brand-text {
                display: none;
            }
            .main-content { padding: 1.25rem; }

            #features, #about, #contact, #credits {
                padding: 60px 0 50px;
            }

            .contact-form-wrap {
                padding: 1.75rem;
            }
        }

        @media (max-width: 575.98px) {
            .guest-navbar {
                height: 70px;
            }
            .guest-mobile-menu {
                top: 70px;
            }
            .main-content.full-width {
                margin-top: 70px;
            }
        }
        /* ════════════════════════════
           LOGIN MODAL — TWO PANEL
        ════════════════════════════ */
        .login-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 18, 40, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .login-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* The dialog wrapper */
        .login-modal {
            display: flex;
            width: 100%;
            max-width: 880px;
            min-height: 520px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 32px 100px rgba(0, 0, 0, 0.35);
            transform: translateY(32px) scale(0.97);
            transition: transform 0.38s cubic-bezier(0.34, 1.45, 0.64, 1), opacity 0.3s ease;
            opacity: 0;
            position: relative;
        }

        .login-modal-overlay.active .login-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* ── LEFT PANEL ── */
        .login-panel-left {
            flex: 1;
            background: linear-gradient(155deg, #1e3a8a 0%, #1e40af 45%, #0369a1 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* decorative blobs */
        .login-panel-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(14,165,233,0.25) 0%, transparent 70%);
            pointer-events: none;
        }
        .login-panel-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-left-top {
            position: relative;
            z-index: 1;
        }

        .login-left-logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 2.5rem;
        }

        .login-left-logo img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }

        .login-left-logo-text {
            display: flex;
            flex-direction: column;
        }

        .login-left-logo-title {
            font-size: 1rem;
            font-weight: 700;
            color: white;
            line-height: 1.2;
        }

        .login-left-logo-sub {
            font-size: 0.68rem;
            color: #93c5fd;
            font-weight: 500;
        }

        .login-left-headline {
            font-size: clamp(1.35rem, 2.5vw, 1.75rem);
            font-weight: 800;
            color: white;
            line-height: 1.25;
            margin-bottom: 1rem;
        }

        .login-left-desc {
            font-size: 0.875rem;
            color: #bfdbfe;
            line-height: 1.75;
            margin-bottom: 2rem;
        }

        .login-left-features {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            position: relative;
            z-index: 1;
        }

        .login-left-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .login-left-feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7dd3fc;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .login-left-feature span {
            font-size: 0.82rem;
            color: #bfdbfe;
            font-weight: 500;
        }

        .login-left-bottom {
            position: relative;
            z-index: 1;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.25rem;
            margin-top: 2rem;
        }

        .login-left-bottom p {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            margin: 0;
        }

        /* ── RIGHT PANEL ── */
        .login-panel-right {
            width: 400px;
            flex-shrink: 0;
            background: white;
            padding: 2.5rem 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-modal-close {
            position: absolute;
            top: 1.1rem;
            right: 1.1rem;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            line-height: 1;
            z-index: 10;
        }

        .login-modal-close:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
            transform: rotate(90deg);
        }

        .login-modal-header {
            margin-bottom: 1.6rem;
        }

        .login-modal-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .login-modal-subtitle {
            font-size: 0.82rem;
            color: #94a3b8;
            margin: 0;
        }

        .login-modal-alert {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            margin-bottom: 1.1rem;
        }

        .login-modal-alert--success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }

        .login-modal-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .login-field {
            display: flex;
            flex-direction: column;
        }

        .login-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
        }

        .login-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-input-icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1;
        }

        .login-input {
            width: 100%;
            padding: 0.72rem 1rem 0.72rem 2.6rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 11px;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s ease;
            outline: none;
        }

        .login-input:focus {
            border-color: var(--primary-blue);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .login-input.is-invalid {
            border-color: #f87171;
            background: #fff7f7;
        }

        .login-toggle-pw {
            position: absolute;
            right: 0.9rem;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.95rem;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }

        .login-toggle-pw:hover { color: var(--primary-blue); }

        .login-forgot {
            font-size: 0.75rem;
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-forgot:hover {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        .login-remember { margin-top: -0.15rem; }

        .login-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.82rem;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .login-checkbox-label input[type="checkbox"] { display: none; }

        .login-checkbox-custom {
            width: 17px; height: 17px;
            border: 1.5px solid #cbd5e1;
            border-radius: 5px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .login-checkbox-label input[type="checkbox"]:checked + .login-checkbox-custom {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .login-checkbox-label input[type="checkbox"]:checked + .login-checkbox-custom::after {
            content: '✓';
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1;
        }

        .login-submit-btn {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: white;
            border: none;
            padding: 0.85rem;
            border-radius: 11px;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .login-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        .login-submit-btn:active { transform: translateY(0); }

        /* Responsive — stack panels on small screens */
        @media (max-width: 700px) {
            .login-modal {
                flex-direction: column;
                max-width: 420px;
                min-height: unset;
                border-radius: 22px;
            }
            .login-panel-left {
                padding: 2rem 1.75rem 1.5rem;
            }
            .login-left-features { display: none; }
            .login-left-bottom   { display: none; }
            .login-left-desc     { margin-bottom: 0; }
            .login-panel-right {
                width: 100%;
                padding: 2rem 1.75rem 1.75rem;
            }
        }

        @media (max-width: 460px) {
            .login-modal {
                border-radius: 18px;
            }
            .login-panel-left {
                padding: 1.5rem 1.25rem 1.25rem;
            }
            .login-left-headline { font-size: 1.1rem; }
            .login-panel-right {
                padding: 1.5rem 1.25rem 1.5rem;
            }
        }

    </style>
</head>
<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ══════════════════════════════════════════════════════════
         GUEST NAVBAR (Modern Design)
    ═══════════════════════════════════════════════════════════ -->
    @guest
        <nav class="guest-navbar" id="guestNavbar">
            <div class="guest-navbar-container">
                <!-- Brand -->
                <a href="/" class="guest-navbar-brand">
                    <img src="{{ asset('img/cnhs.png') }}" alt="CNHS Logo" class="guest-brand-logo">
                    <div class="guest-brand-text">
                        <div class="guest-brand-title">CNHS Attendance</div>
                        <div class="guest-brand-subtitle">Concepcion National High School</div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="guest-navbar-menu">
                    <ul class="guest-nav-links">
                        <li><a href="#home" class="guest-nav-link active">Home</a></li>
                        <li><a href="#features" class="guest-nav-link">Features</a></li>
                        <li><a href="#about" class="guest-nav-link">About</a></li>
                        <li><a href="#contact" class="guest-nav-link">Contact</a></li>
                    </ul>
                    <div class="guest-navbar-actions">
                        <button type="button" class="btn-guest-primary" id="openLoginModal">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Login</span>
                        </button>
                    </div>
                </div>

                <!-- Mobile Toggle -->
                <button class="guest-mobile-toggle" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="guest-mobile-menu" id="guestMobileMenu">
            <ul class="guest-nav-links">
                <li><a href="#home" class="guest-nav-link active">Home</a></li>
                <li><a href="#features" class="guest-nav-link">Features</a></li>
                <li><a href="#about" class="guest-nav-link">About</a></li>
                <li><a href="#contact" class="guest-nav-link">Contact</a></li>
            </ul>
            <button type="button" class="btn-guest-primary" id="openLoginModalMobile">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </button>
        </div>

        <!-- ══════════════════════════════════════════════════════
             LOGIN MODAL — TWO PANEL
        ═══════════════════════════════════════════════════════ -->
        <div class="login-modal-overlay" id="loginModalOverlay">
            <div class="login-modal" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">

                <!-- ── LEFT PANEL: System Description ── -->
                <div class="login-panel-left">
                    <div class="login-left-top">
                        <!-- Logo + School Name -->
                        <div class="login-left-logo">
                            <img src="{{ asset('img/cnhs.png') }}" alt="CNHS Logo">
                            <div class="login-left-logo-text">
                                <div class="login-left-logo-title">CNHS Attendance</div>
                                <div class="login-left-logo-sub">Concepcion National High School</div>
                            </div>
                        </div>

                        <!-- Headline -->
                        <h2 class="login-left-headline">
                            Smart Attendance<br>Management System
                        </h2>

                        <!-- Description -->
                        <p class="login-left-desc">
                            A modern RFID-powered attendance system built for Concepcion National High School — Concepcion, Mabini, Bohol. Accurate, fast, and paperless.
                        </p>
                    </div>

                    <!-- Feature Highlights -->
                    <div class="login-left-features">
                        <div class="login-left-feature">
                            <div class="login-left-feature-icon">
                                <i class="bi bi-credit-card-2-front"></i>
                            </div>
                            <span>RFID card tap-to-record attendance</span>
                        </div>
                        <div class="login-left-feature">
                            <div class="login-left-feature-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <span>Real-time dashboard & analytics</span>
                        </div>
                        <div class="login-left-feature">
                            <div class="login-left-feature-icon">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                            <span>Automated downloadable reports</span>
                        </div>
                        <div class="login-left-feature">
                            <div class="login-left-feature-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <span>Secure role-based admin access</span>
                        </div>
                    </div>

                    <div class="login-left-bottom">
                        <p>&copy; {{ date('Y') }} CNHS Smart Attendance &mdash; v1.0.2026</p>
                    </div>
                </div>

                <!-- ── RIGHT PANEL: Login Form ── -->
                <div class="login-panel-right">

                    <!-- Close Button -->
                    <button class="login-modal-close" id="closeLoginModal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>

                    <!-- Header -->
                    <div class="login-modal-header">
                        <h2 class="login-modal-title" id="loginModalTitle">Welcome Back</h2>
                        <p class="login-modal-subtitle">Sign in to your administrator account</p>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="login-modal-alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="login-modal-alert login-modal-alert--success">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="login-modal-form">
                        @csrf

                        <div class="login-field">
                            <label for="email" class="login-label">Email Address</label>
                            <div class="login-input-wrap">
                                <i class="bi bi-envelope login-input-icon"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="login-input @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="you@example.com"
                                    required
                                    autocomplete="email"
                                >
                            </div>
                        </div>

                        <div class="login-field">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="login-label mb-0">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="login-forgot">Forgot password?</a>
                                @endif
                            </div>
                            <div class="login-input-wrap">
                                <i class="bi bi-lock login-input-icon"></i>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="login-input @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                >
                                <button type="button" class="login-toggle-pw" id="togglePassword" aria-label="Toggle password">
                                    <i class="bi bi-eye" id="togglePwIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="login-remember">
                            <label class="login-checkbox-label">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="login-checkbox-custom"></span>
                                <span>Remember me</span>
                            </label>
                        </div>

                        <button type="submit" class="login-submit-btn">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Sign In
                        </button>
                    </form>

                </div><!-- end right panel -->

            </div>
        </div>
    @endguest

    <!-- ══════════════════════════════════════════════════════════
         ADMIN SIDEBAR & NAVBAR
    ═══════════════════════════════════════════════════════════ -->
    @auth
        @if(auth()->user()->is_admin)
            <nav class="sidebar" id="sidebar">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('img/cnhs.png') }}" alt="CNHS Logo">
                </a>

                <div class="sidebar-nav">
                    <div class="nav-section-label">Main Menu</div>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('students.create') }}"
                               class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                                <span>Students</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('attendance.index') }}"
                               class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="bi bi-clipboard-check-fill"></i></span>
                                <span>Attendance</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}"
                               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></span>
                                <span>Reports</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-footer">
                    <div class="sidebar-footer-text">
                        <i class="bi bi-info-circle"></i> Version 1.0.2026
                    </div>
                </div>
            </nav>

            <nav class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle-btn" id="sidebarToggle">
                        <i class="bi bi-list" style="font-size:1.2rem;"></i>
                    </button>
                    <span class="navbar-title">CNHS Smart Attendance Management System</span>
                </div>

                <div class="navbar-right">
                    <span class="user-badge">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="button" class="btn btn-logout" onclick="confirmLogout()">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </nav>
        @endif
    @endauth

    <!-- ══════════════════════════════════════════════════════════
         PAGE CONTENT
    ═══════════════════════════════════════════════════════════ -->
    <main class="main-content {{ auth()->guest() ? 'full-width' : '' }}">
        @yield('content')

        @guest
        <!-- ══════════════════════════════════════════════════════
             HOME / HERO SECTION
        ═══════════════════════════════════════════════════════ -->
        <section id="home">
            <div class="hero-grid-pattern"></div>
            <div class="container hero-content">
                <div class="row align-items-center g-5">

                    <!-- Left: Text -->
                    <div class="col-lg-6">
                        <div class="hero-eyebrow">
                            <i class="bi bi-broadcast"></i> Live Attendance System
                        </div>
                        <h1 class="hero-title">
                            Smart Attendance<br>
                            for <span class="hero-highlight">Concepcion NHS</span>
                        </h1>
                        <p class="hero-desc">
                            A modern RFID-powered attendance management system built for Concepcion National High School — Mabini, Bohol. Fast, paperless, and always accurate.
                        </p>
                        <div class="hero-actions">
                            <button type="button" class="btn-hero-primary" id="heroLoginBtn">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Get Started
                            </button>
                            <a href="#features" class="btn-hero-secondary">
                                <i class="bi bi-arrow-down-circle"></i>
                                See Features
                            </a>
                        </div>

                        <div class="hero-stats">
                            <div class="hero-stat">
                                <span class="hero-stat-number">100%</span>
                                <span class="hero-stat-label">Digital Records</span>
                            </div>
                            <div class="hero-stat-divider"></div>
                            <div class="hero-stat">
                                <span class="hero-stat-number">RFID</span>
                                <span class="hero-stat-label">Tap to Record</span>
                            </div>
                            <div class="hero-stat-divider"></div>
                            <div class="hero-stat">
                                <span class="hero-stat-number">24/7</span>
                                <span class="hero-stat-label">System Access</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info Card -->
                    <div class="col-lg-6">
                        <div class="hero-card">
                            <div class="hero-card-header">
                                <div class="hero-card-logo">
                                    <img src="{{ asset('img/cnhs.png') }}" alt="CNHS Logo">
                                </div>
                                <div class="hero-card-school">
                                    <div class="hero-card-school-name">Concepcion National High School</div>
                                    <div class="hero-card-school-loc">Concepcion, Mabini, Bohol</div>
                                </div>
                                <div class="hero-live-badge">
                                    <div class="hero-live-dot"></div> Live
                                </div>
                            </div>

                            <div class="hero-feature-list">
                                <div class="hero-feature-row">
                                    <div class="hero-feature-row-icon">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </div>
                                    <div class="hero-feature-row-text">
                                        <h6>RFID Card Attendance</h6>
                                        <p>Students tap their RFID card — attendance recorded instantly</p>
                                    </div>
                                </div>
                                <div class="hero-feature-row">
                                    <div class="hero-feature-row-icon">
                                        <i class="bi bi-speedometer2"></i>
                                    </div>
                                    <div class="hero-feature-row-text">
                                        <h6>Real-time Dashboard</h6>
                                        <p>Live statistics and attendance data at your fingertips</p>
                                    </div>
                                </div>
                                <div class="hero-feature-row">
                                    <div class="hero-feature-row-icon">
                                        <i class="bi bi-file-earmark-bar-graph"></i>
                                    </div>
                                    <div class="hero-feature-row-text">
                                        <h6>Automated Reports</h6>
                                        <p>Generate and download attendance reports in seconds</p>
                                    </div>
                                </div>
                                <div class="hero-feature-row">
                                    <div class="hero-feature-row-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="hero-feature-row-text">
                                        <h6>Secure Admin Portal</h6>
                                        <p>Role-based access keeps your data safe and protected</p>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-card-footer">
                                <i class="bi bi-shield-lock"></i>
                                Secure System &bull; CNHS 2026 &bull; v1.0
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Scroll hint -->
            <div class="hero-scroll-hint">
                <span>Scroll</span>
                <i class="bi bi-chevron-double-down"></i>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             FEATURES SECTION
        ═══════════════════════════════════════════════════════ -->
        <section id="features">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow reveal">
                        <i class="bi bi-stars"></i> What We Offer
                    </div>
                    <h2 class="section-title reveal reveal-delay-1">
                        Everything You Need to<br><span>Track Attendance Smarter</span>
                    </h2>
                    <p class="section-subtitle mx-auto reveal reveal-delay-2">
                        A comprehensive set of tools designed to streamline attendance management for Concepcion National High School.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-1">
                        <div class="feature-card">
                            <div class="feature-icon-wrap blue">
                                <i class="bi bi-credit-card-2-front"></i>
                            </div>
                            <h5>RFID Card Scanning</h5>
                            <p>Instantly record student attendance using RFID card tapping. Fast, accurate, and contactless — no more manual roll calls.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="feature-card">
                            <div class="feature-icon-wrap sky">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h5>Real-time Analytics</h5>
                            <p>View live attendance statistics, trends, and insights on a modern dashboard that updates in real time.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="feature-card">
                            <div class="feature-icon-wrap indigo">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                            <h5>Automated Reports</h5>
                            <p>Generate detailed attendance reports by student, section, or date range — downloadable and ready to submit.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="feature-card">
                            <div class="feature-icon-wrap teal">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5>Student Management</h5>
                            <p>Easily register and manage student profiles, sections, and ID assignments from one central place.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="feature-card">
                            <div class="feature-icon-wrap violet">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h5>Secure Admin Access</h5>
                            <p>Role-based authentication ensures only authorized administrators can manage records and settings.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 reveal reveal-delay-4">
                        <div class="feature-card">
                            <div class="feature-icon-wrap green">
                                <i class="bi bi-phone"></i>
                            </div>
                            <h5>Mobile Responsive</h5>
                            <p>Fully optimized for smartphones and tablets — manage attendance from any device, anywhere on campus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             ABOUT SECTION
        ═══════════════════════════════════════════════════════ -->
        <section id="about">
            <div class="container position-relative">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="about-badge reveal">
                            <i class="bi bi-info-circle"></i> About The System
                        </div>
                        <h2 class="about-title reveal reveal-delay-1">
                            Built for CNHS,<br>Designed for Excellence
                        </h2>
                        <p class="about-desc reveal reveal-delay-2">
                            The CNHS Smart Attendance Management System was developed to modernize and simplify how Concepcion National High School tracks student attendance. 
                            By replacing traditional paper-based methods with RFID card technology, we reduce errors, save time, and provide actionable data for teachers and administrators.
                        </p>
                        <p class="about-desc reveal reveal-delay-3" style="margin-bottom:0;">
                            Our system supports the school's commitment to accurate record-keeping, student accountability, and data-driven decision-making — all in one easy-to-use platform.
                        </p>

                        <div class="row g-3 mt-4">
                            <div class="col-4 reveal reveal-delay-2">
                                <div class="about-stat-card">
                                    <div class="about-stat-number">100%</div>
                                    <div class="about-stat-label">Digital Records</div>
                                </div>
                            </div>
                            <div class="col-4 reveal reveal-delay-3">
                                <div class="about-stat-card">
                                    <div class="about-stat-number">Fast</div>
                                    <div class="about-stat-label">RFID Scanning</div>
                                </div>
                            </div>
                            <div class="col-4 reveal reveal-delay-4">
                                <div class="about-stat-card">
                                    <div class="about-stat-number">24/7</div>
                                    <div class="about-stat-label">Access Anytime</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 reveal reveal-delay-2">
                        <div class="about-visual">
                            <div class="about-visual-item">
                                <div class="about-visual-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div class="about-visual-text">
                                    <h6>Concepcion National High School</h6>
                                    <p>Concepcion, Mabini, Bohol — A public secondary school committed to quality education and student development.</p>
                                </div>
                            </div>
                            <div class="about-visual-item">
                                <div class="about-visual-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <div class="about-visual-text">
                                    <h6>Academic Focus</h6>
                                    <p>The system covers all grade levels and sections, making attendance tracking unified across the entire school.</p>
                                </div>
                            </div>
                            <div class="about-visual-item">
                                <div class="about-visual-icon">
                                    <i class="bi bi-patch-check"></i>
                                </div>
                                <div class="about-visual-text">
                                    <h6>Reliable & Accurate</h6>
                                    <p>Eliminates human error from manual registers — every RFID tap is automatically timestamped and stored as a verifiable record.</p>
                                </div>
                            </div>
                            <div class="about-visual-item">
                                <div class="about-visual-icon">
                                    <i class="bi bi-code-slash"></i>
                                </div>
                                <div class="about-visual-text">
                                    <h6>Modern Technology Stack</h6>
                                    <p>Built with Laravel and Bootstrap 5 — a modern, secure, and scalable foundation for school management.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             CONTACT SECTION
        ═══════════════════════════════════════════════════════ -->
        <section id="contact">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="section-eyebrow reveal">
                        <i class="bi bi-envelope"></i> Get In Touch
                    </div>
                    <h2 class="section-title reveal reveal-delay-1">
                        We're Here to <span>Help You</span>
                    </h2>
                    <p class="section-subtitle mx-auto reveal reveal-delay-2">
                        Have questions about the system or need technical support? Reach out to us through any of the channels below.
                    </p>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4 reveal reveal-delay-1">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <h5>Location</h5>
                            <p>Concepcion National High School</p>
                            <a href="https://maps.google.com/?q=Concepcion+Mabini+Bohol" target="_blank">Concepcion, Mabini, Bohol</a>
                        </div>
                    </div>
                    <div class="col-md-4 reveal reveal-delay-2">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <h5>Email Us</h5>
                            <p>For inquiries and support</p>
                            <a href="mailto:admin@cnhs.edu.ph">admin@cnhsmabini.edu.ph</a>
                        </div>
                    </div>
                    <div class="col-md-4 reveal reveal-delay-3">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <h5>Call Us</h5>
                            <p>Monday – Friday, 8AM – 5PM</p>
                            <a href="tel:+639000000000">(075) 000-0000</a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="row justify-content-center">
                    <div class="col-lg-8 reveal reveal-delay-2">
                        <div class="contact-form-wrap">
                            <h4 class="fw-700 mb-1" style="font-weight:700; color:#0f172a;">Send a Message</h4>
                            <p class="text-muted mb-4" style="font-size:0.9rem;">Fill out the form below and we'll get back to you as soon as possible.</p>
                            <form id="contactForm" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contactName" placeholder="e.g. Juan Dela Cruz" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="contactEmail" placeholder="you@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                                        <select class="form-select" id="contactSubject" required>
                                            <option value="" disabled selected>Select a subject</option>
                                            <option value="Technical Support">Technical Support</option>
                                            <option value="Account Issues">Account Issues</option>
                                            <option value="Feature Request">Feature Request</option>
                                            <option value="General Inquiry">General Inquiry</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="contactMessage" rows="5" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn-contact-submit">
                                            <i class="bi bi-send-fill me-2"></i> Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             DEVELOPER CREDITS SECTION
        ═══════════════════════════════════════════════════════ -->
        <section id="credits">
            <div class="container">
                <div class="text-center mb-5">
                    <div class="credits-eyebrow reveal">
                        <i class="bi bi-code-slash"></i> Developer
                    </div>
                    <h2 class="section-title reveal reveal-delay-1">
                        Built With <span>Passion &amp; Purpose</span>
                    </h2>
                    <p class="section-subtitle mx-auto reveal reveal-delay-2">
                        This system was designed and developed by a Computer Science graduate dedicated to modernizing attendance management at Concepcion National High School.
                    </p>
                </div>

                <div class="row justify-content-center g-4">
                    <div class="col-lg-8 reveal reveal-delay-1">
                        <div class="dev-card" id="devCardBtn" style="cursor:pointer;" title="Click to view full profile">
                            <div class="dev-avatar" id="devAvatar">
                                <img src="{{ asset('img/nikkerson.jpg') }}" alt="Nikkerson Doydora">
                            </div>
                            <div class="dev-info">
                                <div class="dev-role">
                                    <i class="bi bi-terminal"></i> Lead Developer
                                </div>
                                <div class="dev-name">Nikkerson Doydora</div>
                                <div class="dev-course">BS Computer Science &mdash; 2025</div>
                                <div class="dev-tags">
                                    <span class="dev-tag">Laravel</span>
                                    <span class="dev-tag">Bootstrap 5</span>
                                    <span class="dev-tag">RFID Integration</span>
                                    <span class="dev-tag">MySQL</span>
                                    <span class="dev-tag">PHP</span>
                                </div>
                            </div>
                            <div class="ms-auto ps-2 flex-shrink-0">
                                <button type="button" class="btn-hero-primary" style="padding:0.55rem 1.25rem; font-size:0.8rem;" onclick="event.stopPropagation(); openDevModal();">
                                    <i class="bi bi-person-lines-fill"></i> View Profile
                                </button>
                            </div>
                        </div>


                <div class="text-center mt-4 reveal reveal-delay-2">
                    <div class="credits-note d-inline-flex">
                        <i class="bi bi-code-slash"></i>
                        <span>Designed and developed by <strong>Nikkerson Doydora</strong> &mdash; Concepcion National High School, Mabini, Bohol &bull; {{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Guest Footer -->
        <footer class="guest-footer">
            <div class="container">
                <p class="mb-1">
                    &copy; {{ date('Y') }} <strong style="color:#e2e8f0;">CNHS Smart Attendance Management System</strong>. All rights reserved.
                </p>
                <p class="mb-0" style="font-size:0.78rem;">
                    Concepcion National High School &mdash; Concepcion, Mabini, Bohol
                </p>
            </div>
        </footer>
        @endguest
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Logout Confirmation
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your account",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Admin Sidebar Toggle
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar   = document.getElementById('sidebar');
            const overlay   = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar?.classList.add('show');
                overlay?.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar?.classList.remove('show');
                overlay?.classList.remove('show');
                document.body.style.overflow = '';
            }

            toggleBtn?.addEventListener('click', () =>
                sidebar?.classList.contains('show') ? closeSidebar() : openSidebar()
            );

            overlay?.addEventListener('click', closeSidebar);

            sidebar?.querySelectorAll('.nav-link').forEach(link =>
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) closeSidebar();
                })
            );

            // Guest Navbar - Mobile Menu Toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const guestMobileMenu = document.getElementById('guestMobileMenu');
            
            mobileMenuToggle?.addEventListener('click', function() {
                guestMobileMenu?.classList.toggle('show');
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-list');
                icon.classList.toggle('bi-x-lg');
            });

            // Close mobile menu when clicking a link
            document.querySelectorAll('.guest-mobile-menu .guest-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    guestMobileMenu?.classList.remove('show');
                    const icon = mobileMenuToggle?.querySelector('i');
                    icon?.classList.add('bi-list');
                    icon?.classList.remove('bi-x-lg');
                });
            });

            // Guest Navbar Scroll Effect
            const guestNavbar = document.getElementById('guestNavbar');
            if (guestNavbar) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        guestNavbar.classList.add('scrolled');
                    } else {
                        guestNavbar.classList.remove('scrolled');
                    }
                });
            }

            // Smooth Scroll for Guest Nav Links
            document.querySelectorAll('.guest-nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            const offset = 88;
                            const targetPosition = target.getBoundingClientRect().top + window.scrollY - offset;
                            window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                        }
                        // Update active state on all nav links (desktop + mobile)
                        document.querySelectorAll('.guest-nav-link').forEach(l => l.classList.remove('active'));
                        document.querySelectorAll(`.guest-nav-link[href="${href}"]`).forEach(l => l.classList.add('active'));
                    }
                });
            });

            // Active Nav Link on Scroll (Intersection Observer)
            const sections = document.querySelectorAll('section[id], div[id="home"]');
            if (sections.length) {
                const navObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const id = '#' + entry.target.id;
                            document.querySelectorAll('.guest-nav-link').forEach(l => {
                                l.classList.toggle('active', l.getAttribute('href') === id);
                            });
                        }
                    });
                }, { threshold: 0.4, rootMargin: '-80px 0px -40% 0px' });

                sections.forEach(s => navObserver.observe(s));
            }

            // Scroll Reveal Animation
            const revealEls = document.querySelectorAll('.reveal');
            if (revealEls.length) {
                const revealObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });

                revealEls.forEach(el => revealObserver.observe(el));
            }

            // Contact Form Submission
            const contactForm = document.getElementById('contactForm');
            contactForm?.addEventListener('submit', function(e) {
                e.preventDefault();

                const name    = document.getElementById('contactName')?.value.trim();
                const email   = document.getElementById('contactEmail')?.value.trim();
                const subject = document.getElementById('contactSubject')?.value;
                const message = document.getElementById('contactMessage')?.value.trim();

                if (!name || !email || !subject || !message) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Form',
                        text: 'Please fill in all required fields before submitting.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                // Simulate submission (replace with actual AJAX/fetch call to your backend)
                Swal.fire({
                    icon: 'success',
                    title: 'Message Sent!',
                    html: `Thank you, <strong>${name}</strong>! Your message has been received. We'll get back to you at <strong>${email}</strong> soon.`,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Great!'
                }).then(() => {
                    contactForm.reset();
                });
            });

            // ── LOGIN MODAL ──────────────────────────────────────────
            const loginOverlay  = document.getElementById('loginModalOverlay');
            const openBtns      = [
                document.getElementById('openLoginModal'),
                document.getElementById('openLoginModalMobile'),
                document.getElementById('heroLoginBtn')
            ];
            const closeBtn      = document.getElementById('closeLoginModal');
            const togglePwBtn   = document.getElementById('togglePassword');
            const togglePwIcon  = document.getElementById('togglePwIcon');
            const passwordInput = document.getElementById('password');

            function openLoginModal() {
                loginOverlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
                sessionStorage.removeItem('loginModalDismissed');
                setTimeout(() => document.getElementById('email')?.focus(), 350);
            }

            function closeLoginModal() {
                loginOverlay?.classList.remove('active');
                document.body.style.overflow = '';
                sessionStorage.setItem('loginModalDismissed', '1');
            }

            openBtns.forEach(btn => btn?.addEventListener('click', () => {
                // Also close mobile menu if open
                guestMobileMenu?.classList.remove('show');
                const icon = mobileMenuToggle?.querySelector('i');
                icon?.classList.add('bi-list');
                icon?.classList.remove('bi-x-lg');
                openLoginModal();
            }));

            closeBtn?.addEventListener('click', closeLoginModal);

            // Close on overlay click (outside the modal box)
            loginOverlay?.addEventListener('click', function(e) {
                if (e.target === loginOverlay) closeLoginModal();
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && loginOverlay?.classList.contains('active')) {
                    closeLoginModal();
                }
            });

            // Toggle password visibility
            togglePwBtn?.addEventListener('click', function() {
                const isHidden = passwordInput?.type === 'password';
                if (passwordInput) passwordInput.type = isHidden ? 'text' : 'password';
                togglePwIcon?.classList.toggle('bi-eye', !isHidden);
                togglePwIcon?.classList.toggle('bi-eye-slash', isHidden);
            });

            // Auto-open modal if there are validation errors (after failed login)
            const hasErrors = document.querySelector('.login-modal-alert');
            if (hasErrors) openLoginModal();

            // ── DEVELOPER PROFILE MODAL ──────────────────────────────
            const devOverlay = document.getElementById('devModalOverlay');

            window.openDevModal = function() {
                devOverlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            function closeDevModal() {
                devOverlay?.classList.remove('active');
                document.body.style.overflow = '';
            }

            document.getElementById('closeDevModal')?.addEventListener('click', closeDevModal);
            document.getElementById('devCardBtn')?.addEventListener('click', openDevModal);
            devOverlay?.addEventListener('click', function(e) {
                if (e.target === devOverlay) closeDevModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && devOverlay?.classList.contains('active')) closeDevModal();
            });

            // Tab switching
            document.querySelectorAll('.dev-modal-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.dev-modal-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.dev-modal-panel').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('tab-' + this.dataset.tab)?.classList.add('active');
                });
            });
        });
    </script>

    @stack('scripts')

    @guest

        <!-- ══════════════════════════════════════════════════════
             DEVELOPER PROFILE MODAL
        ═══════════════════════════════════════════════════════ -->
        <div class="dev-modal-overlay" id="devModalOverlay">
            <div class="dev-modal" id="devModal" role="dialog" aria-modal="true">

                <!-- Top Bar -->
                <div class="dev-modal-topbar">
                    <div class="dev-modal-topbar-label">
                        <i class="bi bi-code-slash"></i> Developer & System Info
                    </div>
                    <button class="dev-modal-close-btn" id="closeDevModal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Hero Band -->
                <div class="dev-modal-hero">
                    <div class="dev-modal-hero-photo">
                        <img src="{{ asset('img/nikkerson.jpg') }}" alt="Nikkerson Doydora">
                    </div>
                    <div class="dev-modal-hero-info">
                        <div class="dev-modal-hero-name">Nikkerson Doydora</div>
                        <div class="dev-modal-hero-title">Lead Developer &mdash; BS Computer Science</div>
                        <div class="dev-modal-hero-badges">
                            <span class="dev-modal-hero-badge"><i class="bi bi-terminal"></i> Full-Stack Dev</span>
                            <span class="dev-modal-hero-badge"><i class="bi bi-cpu"></i> CS Graduate</span>
                            <span class="dev-modal-hero-badge"><i class="bi bi-geo-alt"></i> Mabini, Bohol</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="dev-modal-tabs">
                    <button class="dev-modal-tab active" data-tab="developer">
                        <i class="bi bi-person-badge"></i> Developer
                    </button>
                    <button class="dev-modal-tab" data-tab="system">
                        <i class="bi bi-building"></i> About System
                    </button>
                    <button class="dev-modal-tab" data-tab="stack">
                        <i class="bi bi-layers"></i> Tech Stack
                    </button>
                </div>

                <!-- Panels -->
                <div class="dev-modal-panels">

                    <!-- DEVELOPER Panel -->
                    <div class="dev-modal-panel active" id="tab-developer">
                        <div class="dev-modal-section-head">Personal Information</div>
                        <div class="dev-info-grid">
                            <div class="dev-info-cell">
                                <label>Full Name</label>
                                <span>Nikkerson Doydora</span>
                            </div>
                            <div class="dev-info-cell">
                                <label>Degree</label>
                                <span>BS Computer Science</span>
                            </div>
                            <div class="dev-info-cell">
                                <label>Role</label>
                                <span>Lead Developer</span>
                            </div>
                            <div class="dev-info-cell">
                                <label>Location</label>
                                <span>Concepcion, Mabini, Bohol, Philippines</span>
                            </div>
                            <div class="dev-info-cell">
                                <label>College</label>
                                <span>Bohol Island State University - Candijay Campus</span>
                            </div>
                            <div class="dev-info-cell">
                                <label>Year</label>
                                <span>2025</span>
                            </div>
                        </div>

                        <div class="dev-modal-section-head">About the Developer</div>
                        <div class="dev-about-text">
                            Nikkerson Doydora is a BS Computer Science graduate with a strong passion for building practical, real-world software solutions. He designed and developed the CNHS Smart Attendance Management System from the ground up — handling everything from system architecture and database design to UI/UX and RFID hardware integration. His goal was to replace outdated paper-based attendance methods with a fast, reliable, and modern digital platform tailored specifically for Concepcion National High School.
                        </div>
                    </div>

                    <!-- SYSTEM Panel -->
                    <div class="dev-modal-panel" id="tab-system">
                        <div class="dev-system-desc">
                            The CNHS Smart Attendance Management System is a web-based platform developed to modernize how Concepcion National High School tracks and manages daily student attendance. It replaces manual paper logs with an RFID-powered digital system that is fast, accurate, and easy to manage.
                        </div>

                        <div class="dev-modal-section-head">Key Features</div>
                        <div class="dev-system-card">
                            <div class="dev-system-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
                            <div>
                                <h6>RFID Card Attendance</h6>
                                <p>Students tap their assigned RFID card to record attendance instantly — no manual input, no errors, no delays.</p>
                            </div>
                        </div>
                        <div class="dev-system-card">
                            <div class="dev-system-card-icon"><i class="bi bi-speedometer2"></i></div>
                            <div>
                                <h6>Real-time Dashboard</h6>
                                <p>Administrators see live attendance statistics, charts, and trends updated in real time across all sections and grade levels.</p>
                            </div>
                        </div>
                        <div class="dev-system-card">
                            <div class="dev-system-card-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                            <div>
                                <h6>Automated Reports</h6>
                                <p>Generate and download detailed attendance reports by student, section, or date range — ready to submit in seconds.</p>
                            </div>
                        </div>
                        <div class="dev-system-card">
                            <div class="dev-system-card-icon"><i class="bi bi-shield-lock"></i></div>
                            <div>
                                <h6>Secure Role-Based Access</h6>
                                <p>Only authorized administrators can access records, manage students, and configure system settings.</p>
                            </div>
                        </div>
                        <div class="dev-system-card">
                            <div class="dev-system-card-icon"><i class="bi bi-people"></i></div>
                            <div>
                                <h6>Student Management</h6>
                                <p>Register students, assign RFID cards, organize by section, and manage all profiles from one central interface.</p>
                            </div>
                        </div>
                    </div>

                    <!-- STACK Panel -->
                    <div class="dev-modal-panel" id="tab-stack">
                        <div class="dev-modal-section-head">Backend</div>
                        <div class="dev-stack-wrap" style="margin-bottom:1.25rem;">
                            <span class="dev-stack-chip"><i class="bi bi-filetype-php"></i> PHP 8</span>
                            <span class="dev-stack-chip"><i class="bi bi-box"></i> Laravel 11</span>
                            <span class="dev-stack-chip"><i class="bi bi-database"></i> MySQL</span>
                            <span class="dev-stack-chip"><i class="bi bi-shield-check"></i> Laravel Auth</span>
                        </div>

                        <div class="dev-modal-section-head">Frontend</div>
                        <div class="dev-stack-wrap" style="margin-bottom:1.25rem;">
                            <span class="dev-stack-chip"><i class="bi bi-filetype-html"></i> HTML5</span>
                            <span class="dev-stack-chip"><i class="bi bi-filetype-css"></i> CSS3</span>
                            <span class="dev-stack-chip"><i class="bi bi-filetype-js"></i> JavaScript</span>
                            <span class="dev-stack-chip"><i class="bi bi-bootstrap"></i> Bootstrap 5</span>
                            <span class="dev-stack-chip"><i class="bi bi-stars"></i> SweetAlert2</span>
                        </div>

                        <div class="dev-modal-section-head">Hardware & Tools</div>
                        <div class="dev-stack-wrap">
                            <span class="dev-stack-chip"><i class="bi bi-credit-card-2-front"></i> RFID Integration</span>
                            <span class="dev-stack-chip"><i class="bi bi-git"></i> Git</span>
                            <span class="dev-stack-chip"><i class="bi bi-terminal"></i> Composer</span>
                            <span class="dev-stack-chip"><i class="bi bi-diagram-3"></i> MVC Architecture</span>
                        </div>
                    </div>

                </div><!-- end panels -->

                <!-- Bottom Bar -->
                <div class="dev-modal-bottombar">
                    <span><i class="bi bi-building"></i> CNHS Smart Attendance Management System</span>
                    <span>Concepcion, Mabini, Bohol &bull; {{ date('Y') }}</span>
                </div>

            </div>
        </div>
    @endguest

</body>
</html>