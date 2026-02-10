<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">

        <!-- Logo -->
        <a href="/" class="text-lg font-bold text-gray-800">
            CNHS Smart Attendance Management System
        </a>

        <!-- Right Side -->
        <div class="flex items-center gap-4">

            @guest
                <div class="flex items-center gap-2 text-sm text-yellow-700 bg-yellow-100 px-3 py-1.5 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                    </svg>
                    No account detected
                </div>
            @endguest

            @auth
                <span class="text-sm text-gray-700">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-red-600 hover:text-red-800">
                        Logout
                    </button>
                </form>
            @endauth

        </div>
    </div>
</nav>
