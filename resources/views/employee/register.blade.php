<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration - DTR System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .animated-square {
            position: absolute;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.22) 60%, rgba(100, 200, 150, 0.13) 100%);
            box-shadow: 0 4px 32px 0 rgba(0, 0, 0, 0.18);
            border: 1.5px solid rgba(180, 255, 200, 0.18);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            will-change: transform;
            pointer-events: none;
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 10;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-white to-green-100 overflow-hidden py-8">
    <!-- Animated Background -->
    <div id="animated-squares" style="position: fixed; inset: 0; pointer-events: none; z-index: 1;"></div>
    <style id="squares-anim-style"></style>

    <div class="login-container w-full max-w-lg mx-4">
        <div class="glass-card p-8 rounded-2xl shadow-2xl">
            <div class="text-center mb-8">
                <div
                    class="bg-green-100 rounded-full p-4 w-20 h-20 mx-auto mb-6 flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
                <p class="text-gray-600">Join the DTR System</p>
            </div>

            <!-- Messages will be handled by SweetAlert 2 -->

            <form method="POST" action="/employee/register">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-3">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                            placeholder="John" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-3">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                            placeholder="Doe" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                        placeholder="john.doe@company.com" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-3">Department</label>
                        <input type="text" name="department" value="{{ old('department') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                            placeholder="HR" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-3">Position</label>
                        <input type="text" name="position" value="{{ old('position') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                            placeholder="Staff" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Password</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                        placeholder="Create a secure password" required>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 bg-white/80"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 px-4 rounded-xl hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 font-semibold shadow-lg transform hover:scale-[1.02]">
                    Create Account
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-600 text-sm mb-4">
                    Already have an account?
                    <a href="/employee/login"
                        class="text-green-600 hover:text-green-800 font-medium transition duration-200">Sign in here</a>
                </p>
                <a href="/admin/login"
                    class="text-gray-500 hover:text-green-600 text-sm font-medium transition duration-200">
                    Admin Login →
                </a>
            </div>
        </div>
    </div>

    <!-- Animated Background Script -->
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

    <!-- SweetAlert 2 Notifications -->
    <script>
        // Show success message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif

        // Show error message
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#16a34a',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 py-2 font-semibold'
                }
            });
        @endif

        // Show validation errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach',
                confirmButtonColor: '#16a34a',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 py-2 font-semibold'
                }
            });
        @endif
    </script>
</body>

</html>
