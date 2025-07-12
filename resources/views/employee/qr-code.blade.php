@extends('layouts.app')

@section('content')
    <x-employee-nav />
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="text-center">
                <div
                    class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Your QR Code</h1>
                <p class="text-gray-600 dark:text-gray-300">Scan this code at the DTR station to clock in/out</p>
            </div>
        </div>

        <!-- QR Code Display -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-center">
                <!-- QR Code Image -->
                <div
                    class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl p-8 mb-6 inline-block shadow-inner">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                        <div class="w-80 h-80 mx-auto flex items-center justify-center">
                            <div id="qrcode" class="qr-container"></div>
                        </div>
                        <!-- QR Code Info -->
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $qrCode }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Scan for instant clock in/out</div>
                        </div>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $user->name }}</h3>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-6 0" />
                            </svg>
                            <span class="font-semibold">{{ $user->employee_id }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ $user->department }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H8a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2" />
                            </svg>
                            <span>{{ $user->position }}</span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6 border border-blue-200 dark:border-blue-800">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">How to use your QR Code:</h4>
                    <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                        <li>• Approach the DTR scanner at your workplace</li>
                        <li>• Hold your QR code in front of the scanner</li>
                        <li>• Wait for the confirmation message</li>
                        <li>• Your time will be automatically recorded</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
                    <button onclick="downloadQR()"
                        class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-blue-500 dark:border-blue-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-semibold">Download</span>
                    </button>
                    <button onclick="printQR()"
                        class="bg-green-600 dark:bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-green-500 dark:border-green-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span class="font-semibold">Print</span>
                    </button>
                    <button onclick="shareQR()"
                        class="bg-purple-600 dark:bg-purple-700 text-white px-6 py-3 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-purple-500 dark:border-purple-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                        </svg>
                        <span class="font-semibold">Share</span>
                    </button>
                    <button onclick="regenerateQR()"
                        class="bg-orange-600 dark:bg-orange-700 text-white px-6 py-3 rounded-lg hover:bg-orange-700 dark:hover:bg-orange-600 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-orange-500 dark:border-orange-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="font-semibold">Regenerate</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alternative Methods -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Alternative Clock In/Out Methods</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 border rounded-lg">
                    <div class="bg-green-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10l4.553-2.276A2 2 0 0020 6.382V5a2 2 0 00-2 2H6a2 2 0 00-2 2v1.382a2 2 0 00.447 1.342L9 10m6 0v4m0 0l-4.553 2.276A2 2 0 014 17.618V19a2 2 0 002 2h12a2 2 0 002-2v-1.382a2 2 0 00-.447-1.342L15 14m0-4h-6" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800">Mobile App</h4>
                    <p class="text-sm text-gray-600">Use the mobile app to scan QR codes</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="bg-blue-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800">Manual Entry</h4>
                    <p class="text-sm text-gray-600">Enter your employee ID manually</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="bg-purple-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800">Face Recognition</h4>
                    <p class="text-sm text-gray-600">Use facial recognition (coming soon)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- QRCode.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .qr-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-container img,
        .qr-container canvas {
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        /* Dark mode specific styles */
        @media (prefers-color-scheme: dark) {

            .qr-container img,
            .qr-container canvas {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
            }
        }

        /* Manual dark mode support */
        .dark .qr-container img,
        .dark .qr-container canvas {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        /* Enhanced button hover effects for dark mode */
        @media (prefers-color-scheme: dark) {
            button:hover {
                transform: translateY(-2px) !important;
            }
        }

        .dark button:hover {
            transform: translateY(-2px) !important;
        }
    </style>
    <script>
        // Generate high-quality QR code
        const qrCodeValue = @json($qrCode);
        const qrCodeEl = document.getElementById('qrcode');
        let qrInstance = null;

        function generateQRCode() {
            // Clear existing QR code
            qrCodeEl.innerHTML = '';

            // Detect dark mode
            const isDarkMode = document.documentElement.classList.contains('dark') ||
                window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Generate new QR code with enhanced settings
            qrInstance = new QRCode(qrCodeEl, {
                text: qrCodeValue,
                width: 300,
                height: 300,
                colorDark: isDarkMode ? "#111827" : "#1f2937", // Darker in dark mode for better contrast
                colorLight: "#ffffff", // Always white for best scanning
                correctLevel: QRCode.CorrectLevel.H, // Highest error correction
                quietZone: 10,
                quietZoneColor: "#ffffff"
            });
        }

        // Initialize QR code on page load
        generateQRCode();

        // Listen for dark mode changes and regenerate QR code
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addListener(function (e) {
                generateQRCode();
            });
        }

        // Also listen for manual dark mode toggle (if implemented)
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    generateQRCode();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        function downloadQR() {
            const img = qrCodeEl.querySelector('img') || qrCodeEl.querySelector('canvas');
            if (!img) {
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Not Ready',
                    text: 'Please wait for the QR code to generate.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            let dataUrl;
            if (img.tagName === 'IMG') {
                dataUrl = img.src;
            } else {
                dataUrl = img.toDataURL('image/png', 1.0); // High quality
            }

            const link = document.createElement('a');
            link.href = dataUrl;
            link.download = 'DTR-QRCode-{{ $user->employee_id }}-{{ date("Y-m-d") }}.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire({
                icon: 'success',
                title: 'Downloaded!',
                text: 'Your QR code has been downloaded successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        function printQR() {
            const img = qrCodeEl.querySelector('img') || qrCodeEl.querySelector('canvas');
            if (!img) {
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Not Ready',
                    text: 'Please wait for the QR code to generate.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            const dataUrl = img.tagName === 'IMG' ? img.src : img.toDataURL('image/png', 1.0);
            const win = window.open('', '_blank');
            win.document.write(`
                                                    <html>
                                                        <head>
                                                            <title>QR Code - {{ $user->name }}</title>
                                                            <style>
                                                                body {
                                                                    margin: 0;
                                                                    padding: 20px;
                                                                    text-align: center;
                                                                    font-family: Arial, sans-serif;
                                                                }
                                                                .qr-print {
                                                                    max-width: 400px;
                                                                    margin: 0 auto;
                                                                    border: 2px solid #ddd;
                                                                    padding: 20px;
                                                                    border-radius: 10px;
                                                                }
                                                                .employee-info {
                                                                    margin-top: 20px;
                                                                    font-size: 14px;
                                                                    color: #666;
                                                                }
                                                                @media print {
                                                                    body { margin: 0; }
                                                                    .qr-print { border: none; }
                                                                }
                                                            </style>
                                                        </head>
                                                        <body>
                                                            <div class="qr-print">
                                                                <h2>{{ $user->name }}</h2>
                                                                <img src="${dataUrl}" style="width: 300px; height: 300px; margin: 20px 0;"/>
                                                                <div class="employee-info">
                                                                    <p><strong>Employee ID:</strong> {{ $user->employee_id }}</p>
                                                                    <p><strong>Department:</strong> {{ $user->department }}</p>
                                                                    <p><strong>QR Code:</strong> ${qrCodeValue}</p>
                                                                    <p><strong>Generated:</strong> ${new Date().toLocaleDateString()}</p>
                                                                </div>
                                                            </div>
                                                        </body>
                                                    </html>
                                                `);
            win.document.close();
            win.print();
        }

        function shareQR() {
            const img = qrCodeEl.querySelector('img') || qrCodeEl.querySelector('canvas');
            if (!img) {
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Not Ready',
                    text: 'Please wait for the QR code to generate.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (navigator.share) {
                const dataUrl = img.tagName === 'IMG' ? img.src : img.toDataURL('image/png', 1.0);
                fetch(dataUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], 'qr-code.png', { type: 'image/png' });
                        navigator.share({
                            title: 'My DTR QR Code',
                            text: 'Here is my DTR QR code for {{ $user->name }}',
                            files: [file]
                        });
                    });
            } else {
                // Fallback for browsers that don't support Web Share API
                const shareText = `My DTR QR Code: ${qrCodeValue}`;
                navigator.clipboard.writeText(shareText).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'QR code text copied to clipboard.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        }

        function regenerateQR() {
            Swal.fire({
                title: 'Regenerate QR Code?',
                text: 'This will create a new QR code. Your old QR code will no longer work.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, regenerate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Generating new QR code...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simulate regeneration (in real app, this would call backend)
                    setTimeout(() => {
                        generateQRCode();
                        Swal.fire({
                            icon: 'success',
                            title: 'QR Code Regenerated!',
                            text: 'Your new QR code is ready to use.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 2000);
                }
            });
        }
    </script>
@endsection