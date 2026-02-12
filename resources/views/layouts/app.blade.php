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
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-blue-dark: #1e40af;
            --primary-blue-light: #3b82f6;
            --accent-blue: #0ea5e9;
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-900: #1e3a8a;
            --sidebar-width: 280px;
            --navbar-height: 76px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-attachment: fixed;
            margin: 0;
        }

        /* Sidebar Styling - FIXED */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            padding: 2rem 1rem;
        }

        .sidebar-brand {
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            align-items: center;
            gap: 0.5rem;
        }

        /* .sidebar-brand:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .sidebar-brand span {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        } */

        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 0.875rem 1.25rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-pills .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent-blue);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-pills .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
        }

        .nav-pills .nav-link:hover::before {
            transform: scaleY(1);
        }

        .nav-pills .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-pills .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-pills .nav-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        /* Top Navbar FIXED */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: white;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }

        .navbar-title {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .user-badge {
            background: var(--blue-50);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-badge i {
            font-size: 1rem;
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            color: white;
        }

        /* Main Content area scrollable */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            overflow-y: auto;
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
            background: rgba(255,255,255,0.95);
            border-radius: 24px 0 0 0;
        }

        /* Full-width content for guest login */
        .main-content.full-width {
            margin: 0;
            border-radius: 0;
            min-height: 100vh;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .top-navbar {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                margin-top: var(--navbar-height);
            }
        }

    </style>
</head>
<body>
<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    @auth
        @if(auth()->user()->is_admin)
            <nav class="sidebar d-flex flex-column">
               <a href="{{ route('dashboard') }}" class="sidebar-brand" style="display:flex; justify-content:center; align-items:center;">
                    <img src="{{ asset('img/cnhs.png') }}" 
                        alt="CNHS Logo" 
                        style="width:100px; height:100px; object-fit:contain; display:block;">
                </a>

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Students</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check-fill me-2"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>

                <div class="mt-auto pt-3" style="border-top:1px solid rgba(255,255,255,0.2)">
                    <div class="text-white-50 small text-center">
                        <i class="bi bi-info-circle me-1"></i>Version 2.0
                    </div>
                </div>
            </nav>
        @endif
    @endauth

    <!-- Main Content -->
    <div class="flex-grow-1 d-flex flex-column">

        <!-- Top Navbar -->
        @auth
            <nav class="top-navbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none text-primary me-3" type="button" id="sidebarToggle">
                        <i class="bi bi-list" style="font-size:1.5rem;"></i>
                    </button>
                    <span class="navbar-title">CNHS Smart Attendance Management System</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="user-badge">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="button" class="btn btn-logout" onclick="confirmLogout()">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main class="main-content {{ auth()->guest() ? 'full-width' : '' }}">
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<script>
    // Logout confirmation
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

    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>

</body>
</html>
