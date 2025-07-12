@extends('layouts.app')

@section('content')
    <x-dash-nav />
    <div class="max-w-xl mx-auto mt-10 bg-white rounded-xl shadow p-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 10l4.553-2.276A2 2 0 0020 6.382V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1.382a2 2 0 00.447 1.342L9 10m6 0v4m0 0l-4.553 2.276A2 2 0 014 17.618V19a2 2 0 002 2h12a2 2 0 002-2v-1.382a2 2 0 00-.447-1.342L15 14m0-4h-6" />
            </svg>
            <span class="font-semibold text-lg text-accent">QR Code Scanner</span>
        </div>

        <!-- Instructions Section -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-3">How to Use:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Scan employee QR codes using the camera</li>
                <li>• Or manually enter employee ID in the input field</li>
                <li>• System will automatically clock in/out employees</li>
            </ul>
        </div>

        <div id="qr-reader" style="width: 100%;"></div>
        <div id="qr-reader-results" class="mt-4 text-center text-lg text-green-600"></div>

        <!-- Manual Input Section -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="font-medium text-gray-700">Manual Entry</span>
            </div>
            <div class="flex gap-2">
                <input type="text" id="manual-user-id" placeholder="Enter User ID"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button onclick="submitManualUserId()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Submit
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">Use this option if the QR scanner is not working</p>
        </div>
    </div>

    <!-- html5-qrcode CDN -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Fix mirrored camera view for QR scanning */
        #qr-reader video {
            transform: scaleX(-1) !important;
        }

        /* Ensure the QR reader container has proper styling */
        #qr-reader {
            border-radius: 8px;
            overflow: hidden;
        }

        /* Style the QR reader controls */
        #qr-reader__dashboard_section {
            padding: 16px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        #qr-reader__dashboard_section button {
            background-color: #3b82f6 !important;
            color: white !important;
            border: none !important;
            padding: 8px 16px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: background-color 0.2s ease !important;
        }

        #qr-reader__dashboard_section button:hover {
            background-color: #2563eb !important;
        }

        #qr-reader__dashboard_section select {
            background-color: white !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
        }
    </style>
    <script>
        let lastResult = null;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                lastResult = decodedText;
                processQRCode(decodedText);
            }
        }

        function onScanFailure(error) {
            // Optionally handle scan errors or ignore
        }



        function submitManualUserId() {
            const userId = document.getElementById('manual-user-id').value.trim();

            if (!userId) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a User ID',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
                return;
            }

            // Clear the input field
            document.getElementById('manual-user-id').value = '';

            // Process the manual entry
            processQRCode(userId);
        }

        function processQRCode(qrCode) {
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send to backend
            fetch('/dtr/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#3B82F6'
                        });

                        // Update results display
                        document.getElementById('qr-reader-results').innerHTML = `
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-green-600">${data.user.name}</div>
                                    <div class="text-sm text-gray-600">${data.user.employee_id} • ${data.user.department}</div>
                                    <div class="text-sm text-blue-600 mt-1">${data.status}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Time In: ${data.time_in} | Time Out: ${data.time_out}
                                    </div>
                                </div>
                            `;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#3B82F6'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing the QR code.',
                        confirmButtonColor: '#3B82F6'
                    });
                });
        }

        // Allow Enter key to submit the form
        document.getElementById('manual-user-id').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                submitManualUserId();
            }
        });

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endsection