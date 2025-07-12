@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border">
            <div class="text-center mb-6">
                <div class="bg-red-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 0v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
                <p class="text-gray-600 text-sm mt-2">Administrator Access Only</p>
            </div>
            <form id="adminLoginForm">
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Email</label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2 bg-white" required>
                </div>
                <div class="mb-6">
                    <label class="block mb-1 font-semibold">Password</label>
                    <div class="relative">
                        <input id="login-password" type="password" name="password"
                            class="w-full border rounded px-3 py-2 pr-10 bg-white" required>
                        <button type="button" id="toggle-password" aria-label="Show password"
                            class="absolute right-2 top-2 focus:outline-none">
                            <!-- Eye icon (visible by default) -->
                            <svg id="eye-icon" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye-off icon (hidden by default) -->
                            <svg id="eye-off-icon" class="w-5 h-5 text-gray-400 hover:text-gray-600 hidden" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" value="1"
                            class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-600">Remember me for 30 days</span>
                    </label>
                </div>
                <button type="submit"
                    class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 active:bg-red-800 transition-colors duration-150">Login</button>
            </form>

            <div class="mt-4 text-center">
                <a href="/employee/login" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Go to Employee Login
                </a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('adminLoginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;
            const remember = document.querySelector('input[name="remember"]').checked;

            // Basic validation
            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Required',
                    text: 'Please enter your email address.',
                    confirmButtonColor: '#DC2626'
                });
                return;
            }

            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Required',
                    text: 'Please enter your password.',
                    confirmButtonColor: '#DC2626'
                });
                return;
            }

            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.',
                    confirmButtonColor: '#DC2626'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Signing in...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send login request
            console.log('Sending login request...', { email: email, remember: remember });
            fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email, password: password, remember: remember })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: data.message,
                            confirmButtonColor: '#DC2626'
                        }).then(() => {
                            // Add longer delay for cloud environments to ensure session is saved
                            setTimeout(() => {
                                console.log('Redirecting to:', data.redirect);
                                window.location.href = data.redirect;
                            }, 1000);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.message,
                            confirmButtonColor: '#DC2626'
                        });
                    }
                })
                .catch(error => {
                    console.error('Login Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to connect to server. Please check your internet connection and try again.',
                        confirmButtonColor: '#DC2626'
                    });
                });
        });
    </script>
    <script>
        const passwordInput = document.getElementById('login-password');
        const toggleBtn = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');

        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', !isPassword);
            eyeOffIcon.classList.toggle('hidden', isPassword);
            toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    </script>
@endsection

@section('background-decor')
    <style>
        .animated-square {
            position: absolute;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.22) 60%, rgba(100, 180, 255, 0.13) 100%);
            box-shadow: 0 4px 32px 0 rgba(0, 0, 0, 0.18);
            border: 1.5px solid rgba(180, 200, 255, 0.18);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            will-change: transform;
            pointer-events: none;
            z-index: 2;
        }
    </style>
    <div id="animated-squares" style="position: fixed; inset: 0; pointer-events: none; z-index: 1;"></div>
    <style id="squares-anim-style"></style>
    <script>
        window.onload = function () {
            const SQUARES = 6;
            const SQUARE_SIZES = [96, 128, 80, 112, 72, 100];
            const SQUARE_OPACITIES = [0.35, 0.32, 0.28, 0.29, 0.25, 0.31];
            const container = document.getElementById('animated-squares');
            const styleTag = document.getElementById('squares-anim-style');
            const windowH = window.innerHeight;
            const windowW = window.innerWidth;
            function randomX(size) {
                return Math.random() * (windowW - size);
            }
            function randomDuration() {
                return 12 + Math.random() * 8; // 12-20s
            }
            function randomSpin() {
                return Math.random() > 0.5 ? 1 : -1;
            }
            function createSquare(i) {
                const size = SQUARE_SIZES[i % SQUARE_SIZES.length];
                const opacity = SQUARE_OPACITIES[i % SQUARE_OPACITIES.length];
                const square = document.createElement('div');
                square.className = 'animated-square';
                square.style.width = `${size}px`;
                square.style.height = `${size}px`;
                square.style.left = `${randomX(size)}px`;
                square.style.top = `${-size - Math.random() * windowH / 2}px`;
                square.style.opacity = opacity;
                square.style.animation = `fall${i} ${randomDuration()}s linear infinite, spin${i} ${8 + Math.random() * 8}s linear infinite`;
                // Keyframes for each square
                const fallKeyframes = `@keyframes fall${i} { 0% { top: ${-size}px; } 100% { top: ${windowH + size}px; } }`;
                const spinKeyframes = `@keyframes spin${i} { 0% { transform: rotate(0deg); } 100% { transform: rotate(${randomSpin() * 360}deg); } }`;
                styleTag.sheet.insertRule(fallKeyframes, styleTag.sheet.cssRules.length);
                styleTag.sheet.insertRule(spinKeyframes, styleTag.sheet.cssRules.length);
                // When animation ends, respawn at top
                square.addEventListener('animationiteration', () => {
                    square.style.left = `${randomX(size)}px`;
                });
                return square;
            }
            for (let i = 0; i < SQUARES; i++) {
                container.appendChild(createSquare(i));
            }
        }
    </script>
@endsection