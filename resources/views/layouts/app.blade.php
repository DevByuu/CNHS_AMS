<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CNHS Smart Attendance Management System</title>
    <link rel="icon" href="{{ asset('img/cnhs.png') }}" type="image/png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-image: url("{{ asset('img/bg.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        /* ════════════════════════════
           SIDEBAR
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
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* scrollbar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        /* Brand */
        .sidebar-brand {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.75rem 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.75rem;
            text-decoration: none;
            transition: background 0.2s;
        }

        .sidebar-brand:hover { background: rgba(255,255,255,0.05); }

        .sidebar-brand img {
            width: 72px; height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }

        .sidebar-brand:hover img { transform: scale(1.05); }

        /* Nav */
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
            overflow: hidden;
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

        .nav-pills .nav-link:hover .nav-icon {
            background: rgba(255,255,255,0.18);
        }

        .nav-pills .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }

        .nav-pills .nav-link.active .nav-icon {
            background: var(--accent-blue);
            box-shadow: 0 4px 12px rgba(14,165,233,0.4);
        }

        /* Active indicator bar */
        .nav-pills .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--accent-blue);
            border-radius: 0 3px 3px 0;
        }

        /* Sidebar footer */
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
           TOP NAVBAR
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
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .top-navbar.guest { left: 0; }

        .navbar-left { display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1; }

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
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: var(--blue-50);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .navbar-title {
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        /* Short title shown only on small screens */
        .navbar-title-short { display: none; }
        .navbar-title-full  { display: inline; }

        .navbar-right { display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; }

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
            white-space: nowrap;
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 0.45rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(220,38,38,0.25);
            white-space: nowrap;
            cursor: pointer;
        }

        .btn-logout:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(220,38,38,0.35);
            color: white;
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
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.full-width {
            margin-left: 0;
            margin-top: var(--navbar-height);
            background: transparent;
        }

        /* ════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1049;
            backdrop-filter: blur(2px);
        }

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
                box-shadow: 8px 0 40px rgba(0,0,0,0.3);
            }
            .sidebar-overlay.show { display: block; }
            .top-navbar { left: 0; }
            .sidebar-toggle-btn { display: flex; }
            .main-content { margin-left: 0; }
        }

        @media (max-width: 767.98px) {
            .top-navbar { padding: 0 1rem; }
            .main-content { padding: 1.25rem; }
            .navbar-title { font-size: 0.85rem; }
            .navbar-title-full  { display: none; }
            .navbar-title-short { display: inline; }
            .user-badge { display: none; }
        }

        @media (max-width: 575.98px) {
            .main-content { padding: 1rem; }
            :root { --navbar-height: 60px; }
        }

        /* ════════════════════════════
           PAGINATION FIX
        ════════════════════════════ */
        .pagination {
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px !important;
            border: 1px solid #e5e7eb;
            color: var(--primary-blue);
            line-height: 1;
        }

        /* Force icons inside pagination to small fixed size */
        .pagination .page-item .page-link i,
        .pagination .page-item .page-link .bi {
            font-size: 0.8rem !important;
            line-height: 1;
            display: flex;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        .pagination .page-item:not(.active) .page-link:hover {
            background: var(--blue-50);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ── Sidebar ── -->
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
        @endif
    @endauth

    <!-- ── Top Navbar ── -->
    <nav class="top-navbar {{ auth()->guest() ? 'guest' : '' }}">
        <div class="navbar-left">
            @auth
                <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list" style="font-size:1.2rem;"></i>
                </button>
            @endauth
            <span class="navbar-title">
                <span class="navbar-title-full">CNHS Smart Attendance Management System</span>
                <span class="navbar-title-short">CNHS Attendance</span>
            </span>
        </div>

        @auth
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
        @endauth
    </nav>

    <!-- ── Page Content ── -->
    <main class="main-content {{ auth()->guest() ? 'full-width' : '' }}">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
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
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4'
                },
                buttonsStyling: false,
                padding: '2rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
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

            // Close on nav link click (mobile)
            sidebar?.querySelectorAll('.nav-link').forEach(link =>
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) closeSidebar();
                })
            );
        });
    </script>

    @stack('scripts')
</body>
</html>