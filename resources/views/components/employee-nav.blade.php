<div class="flex justify-between items-center bg-white rounded-xl p-2 mb-8 shadow-sm w-4/6 mx-auto">
    <div class="flex space-x-2">
        <a href="/employee/dashboard"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('employee/dashboard') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0v6m0 0H7m6 0h6" />
            </svg>
            Dashboard
        </a>
        <a href="/employee/profile"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('employee/profile') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profile
        </a>
        <a href="/employee/history"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('employee/history') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            History
        </a>
        <a href="/employee/qr-code"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('employee/qr-code') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            QR Code
        </a>
    </div>
    <div class="flex items-center space-x-2">
        <!-- Theme toggle is now handled globally in the main layout -->
        <button onclick="confirmLogout()"
            class="flex items-center px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </button>
    </div>
</div>

<script>
    function confirmLogout() {
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/employee/logout';
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '/employee/logout';
            }
        }
    }
</script>