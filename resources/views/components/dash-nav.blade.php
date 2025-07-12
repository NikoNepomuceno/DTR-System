<div class="flex justify-between items-center bg-white rounded-xl p-2 mb-8 shadow-sm">
    <div class="flex space-x-2">
        <a href="/dtr/scan"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('dtr/scan') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 10l4.553-2.276A2 2 0 0021 6.382V5a2 2 0 00-2-2H5a2 2 0 00-2 2v1.382a2 2 0 001.447 1.342L9 10m6 0v4m0 0l-6 3m6-3l6 3m-6-3V6m0 8l-6 3" />
            </svg>
            Scanner
        </a>
        <a href="/dtr"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('dtr') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0v6m0 0H7m6 0h6" />
            </svg>
            Dashboard
        </a>
        <a href="/dtr/employee"
            class="flex items-center px-4 py-2 rounded-lg {{ request()->is('dtr/employee') ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75" />
            </svg>
            Employees
        </a>
        <a href="#" class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            QR Codes
        </a>
    </div>

    <!-- Admin User Info and Logout -->
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-3">
            <div class="bg-red-100 rounded-full p-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 0v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                </svg>
            </div>
            <div class="hidden sm:block">
                <div class="text-sm font-medium text-gray-900">
                    {{ session('user_name', 'Admin') }}
                </div>
                <div class="text-xs text-gray-500">Administrator</div>
            </div>
        </div>

        <a href="#" id="logout-btn"
            class="flex items-center px-4 py-2 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 0v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:inline">Logout</span>
        </a>
    </div>
</div>

<!-- SweetAlert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('logout-btn').addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Logout Confirmation',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-2 font-semibold',
                cancelButton: 'rounded-lg px-6 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading toast
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    timer: 1000,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then(() => {
                    window.location.href = '/logout';
                });
            }
        });
    });
</script>