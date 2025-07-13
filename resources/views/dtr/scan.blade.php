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
                <li>• Use "Switch Camera" button to rotate between front/back cameras on mobile</li>
                <li>• Or manually enter employee ID in the input field</li>
                <li>• System will automatically clock in/out employees</li>
            </ul>
        </div>

        <div id="qr-reader" style="width: 100%;"></div>

        <!-- Camera Controls -->
        <div id="camera-controls" class="mt-4 flex justify-center space-x-4" style="display: none;">
            <button id="switch-camera-btn"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Switch Camera</span>
            </button>
            <button id="stop-camera-btn"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                </svg>
                <span>Stop Camera</span>
            </button>
        </div>

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
        let html5QrCode = null;
        let availableCameras = [];
        let currentCameraIndex = 0;
        let isScanning = false;

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

        // Initialize camera functionality
        async function initializeCamera() {
            try {
                // Get available cameras
                availableCameras = await Html5Qrcode.getCameras();

                if (availableCameras && availableCameras.length > 0) {
                    html5QrCode = new Html5Qrcode("qr-reader");

                    // Show camera controls if multiple cameras are available
                    if (availableCameras.length > 1) {
                        document.getElementById('camera-controls').style.display = 'flex';
                    }

                    // Start with back camera if available, otherwise use first camera
                    currentCameraIndex = findBackCameraIndex();
                    await startCamera();
                } else {
                    // Fallback to Html5QrcodeScanner if no cameras detected
                    let html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", { fps: 10, qrbox: 250 }
                    );
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            } catch (err) {
                console.error('Error initializing camera:', err);
                // Fallback to Html5QrcodeScanner
                let html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", { fps: 10, qrbox: 250 }
                );
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

        function findBackCameraIndex() {
            // Try to find back camera (environment facing)
            for (let i = 0; i < availableCameras.length; i++) {
                const camera = availableCameras[i];
                if (camera.label && camera.label.toLowerCase().includes('back')) {
                    return i;
                }
                if (camera.label && camera.label.toLowerCase().includes('environment')) {
                    return i;
                }
                if (camera.label && camera.label.toLowerCase().includes('rear')) {
                    return i;
                }
            }
            return 0; // Default to first camera
        }

        async function startCamera() {
            if (!html5QrCode || !availableCameras[currentCameraIndex]) return;

            try {
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                const cameraId = availableCameras[currentCameraIndex].id;

                await html5QrCode.start(cameraId, config, onScanSuccess, onScanFailure);
                isScanning = true;

                // Show camera controls
                document.getElementById('camera-controls').style.display = 'flex';

            } catch (err) {
                console.error('Error starting camera:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Camera Error',
                    text: 'Failed to start camera. Please check permissions.',
                    confirmButtonColor: '#3B82F6'
                });
            }
        }

        async function switchCamera() {
            if (!html5QrCode || availableCameras.length <= 1) return;

            try {
                // Stop current camera
                if (isScanning) {
                    await html5QrCode.stop();
                    isScanning = false;
                }

                // Switch to next camera
                currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;

                // Start new camera
                await startCamera();

                // Show feedback
                const currentCamera = availableCameras[currentCameraIndex];
                const cameraName = currentCamera.label || `Camera ${currentCameraIndex + 1}`;

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Switched to: ${cameraName}`,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } catch (err) {
                console.error('Error switching camera:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Switch Failed',
                    text: 'Failed to switch camera. Please try again.',
                    confirmButtonColor: '#3B82F6'
                });
            }
        }

        async function stopCamera() {
            if (!html5QrCode || !isScanning) return;

            try {
                await html5QrCode.stop();
                isScanning = false;

                // Hide camera controls
                document.getElementById('camera-controls').style.display = 'none';

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Camera stopped',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

            } catch (err) {
                console.error('Error stopping camera:', err);
            }
        }

        // Event listeners for camera controls
        document.getElementById('switch-camera-btn').addEventListener('click', switchCamera);
        document.getElementById('stop-camera-btn').addEventListener('click', stopCamera);

        // Initialize camera on page load
        initializeCamera();
    </script>
@endsection