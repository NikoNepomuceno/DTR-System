@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center h-screen overflow-hidden">
        <div class="max-w-md w-full bg-white p-6 rounded-xl shadow-lg border max-h-[90vh] overflow-y-auto">
            <div class="text-center mb-6">
                <div class="bg-green-100 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Employee Registration</h2>
                <p class="text-gray-600 text-sm mt-2">Create your DTR account</p>
            </div>

            <form id="employeeRegisterForm">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Department</label>
                        <select name="department" id="department"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                            <option value="">Select Department</option>
                            <option value="IT">Information Technology</option>
                            <option value="HR">Human Resources</option>
                            <option value="Finance">Finance</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Sales">Sales</option>
                            <option value="Operations">Operations</option>
                            <option value="Customer Service">Customer Service</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Design">Design</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="position" id="position"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g. Software Developer, Manager" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input id="register-password" type="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <button type="button" id="toggle-password" aria-label="Show password"
                            class="absolute right-3 top-3 focus:outline-none text-gray-400 hover:text-gray-600">
                            <!-- Eye icon (visible by default) -->
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye-off icon (hidden by default) -->
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Confirm Password</label>
                    <div class="relative">
                        <input id="register-password-confirm" type="password" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <button type="button" id="toggle-password-confirm" aria-label="Show password"
                            class="absolute right-3 top-3 focus:outline-none text-gray-400 hover:text-gray-600">
                            <!-- Eye icon (visible by default) -->
                            <svg id="eye-icon-confirm" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye-off icon (hidden by default) -->
                            <svg id="eye-off-icon-confirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition duration-200">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Already have an account?
                    <a href="/employee/login" class="text-blue-600 hover:text-blue-700 font-medium">Sign in here</a>
                </p>
            </div>

            <!-- <div class="mt-4 text-center">
                                                                            <a href="/" class="text-gray-500 hover:text-gray-700 text-sm">
                                                                                ← Back to Admin Login
                                                                            </a>
                                                                        </div> -->
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                                                        `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                        `;
            }
        }

        document.getElementById('employeeRegisterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('Form submission started');

            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const department = document.getElementById('department').value;
            const position = document.getElementById('position').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-password-confirm').value;

            // Validation
            if (!firstName) {
                Swal.fire({
                    icon: 'error',
                    title: 'First Name Required',
                    text: 'Please enter your first name.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!lastName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Last Name Required',
                    text: 'Please enter your last name.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!department) {
                Swal.fire({
                    icon: 'error',
                    title: 'Department Required',
                    text: 'Please select your department.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!position) {
                Swal.fire({
                    icon: 'error',
                    title: 'Position Required',
                    text: 'Please enter your position.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Required',
                    text: 'Please enter your email address.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Required',
                    text: 'Please enter a password.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            if (!confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Confirm Password Required',
                    text: 'Please confirm your password.',
                    confirmButtonColor: '#3B82F6'
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
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            // Password strength validation
            if (password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            // Password confirmation validation
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Passwords Don\'t Match',
                    text: 'Please make sure your passwords match.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Creating Account...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send registration request to backend
            const requestData = {
                first_name: firstName,
                last_name: lastName,
                email: email,
                password: password,
                password_confirmation: confirmPassword,
                department: department,
                position: position
            };

            console.log('Sending registration request:', requestData);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);

            if (!csrfToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'Security Error',
                    text: 'CSRF token not found. Please refresh the page and try again.',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            // Create form data instead of JSON
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', confirmPassword);
            formData.append('department', department);
            formData.append('position', position);

            console.log('About to send fetch request');
            fetch('/employee/register', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Response received, status:', response.status);
                    if (!response.ok) {
                        if (response.status === 419) {
                            throw new Error('CSRF token mismatch. Please refresh the page and try again.');
                        }
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Created!',
                            text: data.message,
                            confirmButtonColor: '#3B82F6'
                        }).then(() => {
                            // Redirect to employee dashboard (user is automatically signed in)
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message || 'An error occurred during registration.',
                            confirmButtonColor: '#3B82F6'
                        });
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack,
                        errors: error.errors
                    });

                    // Handle validation errors specifically
                    if (error.errors) {
                        const errorMessages = Object.values(error.errors).flat().join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages,
                            confirmButtonColor: '#3B82F6'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: error.message || 'An error occurred during registration. Please try again.',
                            confirmButtonColor: '#3B82F6'
                        });
                    }
                });
        });

        // Password field toggle
        const passwordInput = document.getElementById('register-password');
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
        // Confirm password field toggle
        const passwordInputConfirm = document.getElementById('register-password-confirm');
        const toggleBtnConfirm = document.getElementById('toggle-password-confirm');
        const eyeIconConfirm = document.getElementById('eye-icon-confirm');
        const eyeOffIconConfirm = document.getElementById('eye-off-icon-confirm');

        toggleBtnConfirm.addEventListener('click', function () {
            const isPassword = passwordInputConfirm.type === 'password';
            passwordInputConfirm.type = isPassword ? 'text' : 'password';
            eyeIconConfirm.classList.toggle('hidden', !isPassword);
            eyeOffIconConfirm.classList.toggle('hidden', isPassword);
            toggleBtnConfirm.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    </script>
@endsection

@section('background-decor')
    <style>
        /* Remove scroll and fix viewport */
        body {
            overflow: hidden !important;
            height: 100vh !important;
        }

        main {
            padding: 0 !important;
            height: 100vh !important;
            overflow: hidden !important;
        }

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